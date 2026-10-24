<?php

namespace App\Services\Accounting;

use App\Models\Product;
use App\Models\StockLedgerEntry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Moving-average stock valuation (ERPNext's default). Every stock movement writes a
 * stock ledger row; the running value must always equal the Inventory account.
 *
 * Call these BEFORE changing products.stock_quantity: when a product has no stock
 * ledger history yet, its current stock is taken as the opening quantity.
 */
class StockValuation
{
    public function __construct(private PostingService $posting)
    {
    }

    /**
     * Stock coming in (purchase, return to stock, positive adjustment).
     * Returns the value added, rounded to cents.
     */
    public function receive(Product $product, float $qty, float $unitCost, ?Model $source = null, $date = null, ?string $batchId = null): float
    {
        [$qtyBefore, $valueBefore, $rateBefore] = $this->position($product);

        $value = round($qty * $unitCost, 2);
        $qtyAfter = round($qtyBefore + $qty, 3);
        $valueAfter = round($valueBefore + $value, 2);
        $rate = ($qtyBefore <= 0 || $qtyAfter <= 0) ? $unitCost : $valueAfter / $qtyAfter;

        if ($qtyBefore < 0) {
            // Stock was oversold: re-base value on the new rate so the books don't carry a negative value.
            $valueAfter = round(max($qtyAfter, 0) * $rate, 2);
            $value = round($valueAfter - $valueBefore, 2);
        }

        $this->record($product, $qty, $rate, $value, $qtyAfter, $valueAfter, $source, $date, $batchId);

        return $value;
    }

    /**
     * Stock going out (sale, negative adjustment) at the current moving-average rate.
     * Returns [unit_rate, value_removed] with value positive.
     */
    public function issue(Product $product, float $qty, ?Model $source = null, $date = null, ?string $batchId = null): array
    {
        [$qtyBefore, $valueBefore, $rate] = $this->position($product);

        $qtyAfter = round($qtyBefore - $qty, 3);
        $value = round($qty * $rate, 2);
        if (abs($qtyAfter) < 0.0005) {
            $value = round($valueBefore, 2); // clear any residue when stock hits zero
        }
        $valueAfter = round($valueBefore - $value, 2);

        $this->record($product, -$qty, $rate, -$value, $qtyAfter, $valueAfter, $source, $date, $batchId);

        return [$rate, $value];
    }

    /** Current [qty, value, rate] for a product. */
    public function position(Product $product): array
    {
        $last = StockLedgerEntry::where('business_id', $product->business_id)
            ->where('product_id', $product->id)
            ->whereNull('batch_id') // drafts from a pending backfill don't count
            ->orderByDesc('id')
            ->first();

        if ($last) {
            return [(float) $last->qty_after, (float) $last->value_after, (float) $last->valuation_rate];
        }

        return $this->openingPosition($product);
    }

    /**
     * First movement for a product that already had stock before the ledger existed:
     * record that stock as opening inventory (Dr Inventory / Cr Opening Balance Equity)
     * so the Inventory account and the stock ledger agree from the start.
     */
    private function openingPosition(Product $product): array
    {
        $qty = round((float) Product::whereKey($product->id)->value('stock_quantity'), 3);
        $rate = $this->fallbackRate($product);
        $value = round(max($qty, 0) * $rate, 2);

        if ($qty == 0) {
            return [0.0, 0.0, $rate];
        }

        $entry = $this->posting->post($product->business_id, [
            'description' => "Opening stock: {$product->name} ({$qty} @ ".number_format($rate, 2).')',
            'source' => $product,
            'event' => 'opening_stock',
            'lines' => [
                ['account' => 'inventory', 'debit' => $value],
                ['account' => 'opening_balance', 'credit' => $value],
            ],
        ]);

        $this->record($product, $qty, $rate, $value, $qty, $value, $product, null, null, $entry?->id);

        return [$qty, $value, $rate];
    }

    private function fallbackRate(Product $product): float
    {
        return (float) ($product->valuation_rate ?? $product->cost_price ?? 0);
    }

    private function record(Product $product, float $qty, float $rate, float $value, float $qtyAfter, float $valueAfter, ?Model $source, $date, ?string $batchId, ?int $journalEntryId = null): void
    {
        StockLedgerEntry::create([
            'business_id' => $product->business_id,
            'product_id' => $product->id,
            'posting_date' => Carbon::parse($date ?? now())->toDateString(),
            'qty_change' => $qty,
            'valuation_rate' => round($rate, 4),
            'value_change' => $value,
            'qty_after' => $qtyAfter,
            'value_after' => $valueAfter,
            'source_type' => $source?->getMorphClass(),
            'source_id' => $source?->getKey(),
            'journal_entry_id' => $journalEntryId,
            'batch_id' => $batchId,
        ]);

        Product::whereKey($product->id)->update(['valuation_rate' => round($rate, 4)]);
        $product->valuation_rate = round($rate, 4);
    }
}
