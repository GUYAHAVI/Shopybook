<?php

namespace App\Services\Accounting;

use App\Models\CommissionPayout;
use App\Models\Cost;
use App\Models\CreditNote;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Payment;
use App\Models\Product;
use App\Models\RentalPayment;
use App\Models\SalaryAdvance;
use App\Models\ServiceBooking;
use App\Models\StockLedgerEntry;
use App\Models\StockReceipt;
use App\Models\SupplierDebt;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Rebuilds a business's books from its operational history (orders, payments,
 * stock receipts, costs, debts, rent, advances, commissions, returns, credit notes)
 * into a DRAFT batch the owner reviews before committing.
 *
 * Stock is replayed against an in-memory position map (products start at zero);
 * at commit a reconciliation entry aligns the books with the physical count.
 */
class BackfillService
{
    /** product_id => ['qty' => float, 'value' => float, 'rate' => float] */
    private array $stock = [];

    private array $warnings = [];

    public function __construct(private PostingService $posting)
    {
    }

    /** Replay all history into a draft batch. Returns ['batch_id' => string, 'warnings' => [...]]. */
    public function run(string $businessId): array
    {
        // Only one draft batch at a time — an old pending batch is replaced
        JournalEntry::where('business_id', $businessId)->whereNotNull('batch_id')->draft()->delete();
        StockLedgerEntry::where('business_id', $businessId)->whereNotNull('batch_id')->delete();

        $batchId = (string) Str::uuid();
        $this->currentBatchId = $batchId;
        $events = $this->timeline($businessId);

        foreach ($events as [$date, $order, $source, $event, $build]) {
            $this->posting->post($businessId, [
                'date' => $date,
                'description' => $build['description'],
                'source' => $source,
                'event' => $event,
                'status' => JournalEntry::DRAFT,
                'batch_id' => $batchId,
                'lines' => $build['lines'],
            ]);
        }

        return ['batch_id' => $batchId, 'warnings' => $this->warnings];
    }

    /** All money/stock events for a business, oldest first (stock arrives before it sells). */
    private function timeline(string $businessId): array
    {
        $out = [];

        foreach (StockReceipt::where('business_id', $businessId)->get() as $r) {
            $out[] = [$r->receipt_date ?? $r->created_at, 1, $r, 'receipt', fn () => $this->receiptLines($r)];
        }
        foreach (Order::where('business_id', $businessId)->where('status', 'completed')->with('items')->get() as $o) {
            $out[] = [$o->created_at, 2, $o, 'sale', fn () => $this->saleLines($o)];
        }
        foreach (Payment::where('business_id', $businessId)->where(fn ($q) => $q->whereNull('status')->orWhereNotIn('status', ['failed', 'cancelled']))->whereNotNull('order_id')->get() as $p) {
            if ($p->status === 'pending' && $p->transaction_id) {
                continue;
            }
            $out[] = [$p->payment_date ?? $p->created_at, 3, $p, 'payment', fn () => $this->paymentLines($p)];
        }
        foreach (ServiceBooking::where('business_id', $businessId)->where('payment_status', 'paid')->get() as $b) {
            $out[] = [$b->service_date ?? $b->created_at, 3, $b, 'service', fn () => [
                ['account' => ChartOfAccounts::paymentMethodKey($b->payment_method), 'debit' => $b->final_amount],
                ['account' => 'service_income', 'credit' => $b->final_amount],
            ]];
        }
        foreach (Cost::where('business_id', $businessId)->get() as $c) {
            $out[] = [$c->date ?? $c->created_at, 3, $c, 'cost', fn () => $this->costLines($c)];
        }
        foreach (SupplierDebt::where('business_id', $businessId)->whereNull('stock_receipt_id')->get() as $d) {
            $out[] = [$d->created_at, 3, $d, 'bill', fn () => [
                ['account' => $d->expense_account_id ?: 'purchases_misc', 'debit' => $d->total_amount],
                ['account' => 'payable', 'credit' => $d->total_amount, 'party' => ['type' => 'supplier', 'id' => $d->supplier_id], 'memo' => $d->supplier_name],
            ]];
        }
        foreach (SupplierPayment::where('business_id', $businessId)->get() as $p) {
            $out[] = [$p->paid_at ?? $p->created_at, 3, $p, 'payment', fn () => [
                ['account' => 'payable', 'debit' => $p->amount, 'party' => ['type' => 'supplier', 'id' => $p->debt?->supplier_id], 'memo' => $p->debt?->supplier_name],
                ['account' => ChartOfAccounts::paymentMethodKey($p->payment_method), 'credit' => $p->amount],
            ]];
        }
        foreach (OrderReturn::where('business_id', $businessId)->where('status', 'completed')->get() as $r) {
            $out[] = [$r->refund_processed_at ?? $r->created_at, 3, $r, 'return', fn () => $this->returnLines($r)];
        }
        foreach (CreditNote::whereHas('order', fn ($q) => $q->where('business_id', $businessId))->where('status', 'approved')->get() as $n) {
            $out[] = [$n->approved_at ?? $n->created_at, 3, $n, 'credit_note', fn () => $this->creditNoteLines($n)];
        }
        foreach (RentalPayment::where('business_id', $businessId)->where('status', 'paid')->get() as $p) {
            $out[] = [$p->paid_at ?? $p->created_at, 3, $p, 'rent', fn () => [
                ['account' => ChartOfAccounts::paymentMethodKey($p->method), 'debit' => $p->amount],
                ['account' => 'rental_income', 'credit' => $p->amount, 'party' => ['type' => 'tenant', 'id' => $p->rental_tenant_id], 'memo' => $p->period],
            ]];
        }
        foreach (SalaryAdvance::where('business_id', $businessId)->where('status', 'paid')->get() as $a) {
            $out[] = [$a->paid_at ?? $a->advance_date ?? $a->created_at, 3, $a, 'advance', fn () => [
                ['account' => 'staff_advances', 'debit' => $a->amount, 'party' => ['type' => 'staff', 'id' => $a->staff_id]],
                ['account' => 'cash', 'credit' => $a->amount],
            ]];
        }
        foreach (CommissionPayout::where('business_id', $businessId)->get() as $c) {
            $out[] = [$c->paid_at ?? $c->created_at, 3, $c, 'commission', fn () => [
                ['account' => 'expense_commission', 'debit' => $c->amount, 'party' => ['type' => 'staff', 'id' => $c->staff_id]],
                ['account' => $c->paid_from_account_id ?: 'cash', 'credit' => $c->amount],
            ]];
        }

        usort($out, fn ($a, $b) => [$a[0], $a[1]] <=> [$b[0], $b[1]]);

        return array_map(fn ($e) => [$e[0], $e[1], $e[2], $e[3], [
            'description' => class_basename($e[2]).' #'.$e[2]->getKey(),
            'lines' => ($e[4])(),
        ]], $out);
    }

    private function receiptLines(StockReceipt $r): array
    {
        $product = Product::find($r->product_id);
        $value = $product ? $this->stockIn($product, (float) $r->quantity_received, (float) $r->unit_cost, $r, $r->receipt_date) : (float) $r->total_cost;

        $credit = $r->payment_mode === 'credit'
            ? ['account' => 'payable', 'credit' => $value, 'party' => ['type' => 'supplier', 'id' => $r->supplier_id], 'memo' => $r->supplier]
            : ['account' => $r->paid_from_account_id ?: 'cash', 'credit' => $value, 'memo' => $r->supplier];

        return [
            ['account' => 'inventory', 'debit' => $value],
            $credit,
        ];
    }

    private function saleLines(Order $o): array
    {
        $cost = 0.0;
        foreach ($o->items as $item) {
            $qty = $item->stockQuantity();
            if ($item->cost_amount !== null) {
                // Cost was snapshotted at sale time — trust it
                $cost += (float) $item->cost_amount;
                if (($product = Product::find($item->product_id))) {
                    $this->stockOut($product, $qty, $o, (float) $item->cost_amount, $o->created_at);
                }
            } else {
                $product = Product::find($item->product_id);
                if ($product) {
                    // Historical item: fall back to the current cost price
                    $cost += $this->stockOut($product, $qty, $o, (float) ($item->unit_cost ?: $product->cost_price ?: 0), $o->created_at);
                } else {
                    $this->warnings[] = "Sale {$o->order_number} has an item with no product record; cost of sales is understated.";
                }
            }
        }

        $total = (float) $o->total_amount;
        $tax = (float) $o->tax;

        return [
            ['account' => 'receivable', 'debit' => $total, 'party' => ['type' => 'customer', 'id' => $o->customer_id]],
            ['account' => 'sales', 'credit' => round($total - $tax, 2)],
            ['account' => 'vat_output', 'credit' => $tax],
            ['account' => 'cogs', 'debit' => $cost],
            ['account' => 'inventory', 'credit' => $cost],
        ];
    }

    private function paymentLines(Payment $p): array
    {
        $order = $p->order;

        return [
            ['account' => ChartOfAccounts::paymentMethodKey($p->payment_method), 'debit' => $p->amount, 'memo' => $p->transaction_reference],
            ['account' => 'receivable', 'credit' => $p->amount, 'party' => ['type' => 'customer', 'id' => $order?->customer_id]],
        ];
    }

    private function costLines(Cost $c): array
    {
        if ($c->type === 'product') {
            $this->warnings[] = "Cost '{$c->description}' is type 'product' — booked as purchases, may duplicate a stock receipt.";
        }

        return [
            ['account' => ChartOfAccounts::costTypeKey($c->type), 'debit' => $c->amount],
            ['account' => $c->paid_from_account_id ?: 'cash', 'credit' => $c->amount],
        ];
    }

    private function returnLines(OrderReturn $r): array
    {
        $order = $r->order;
        $goodsValue = round((float) $r->refund_amount + (float) $r->restocking_fee, 2);
        $taxRatio = $order && (float) $order->total_amount > 0 ? (float) $order->tax / (float) $order->total_amount : 0.0;
        $vat = round($goodsValue * $taxRatio, 2);
        $restock = 0.0;

        if ($r->return_to_stock && $order) {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $restock += $this->stockIn($product, $item->stockQuantity(), (float) ($item->unit_cost ?: $product->cost_price ?: 0), $r, $r->refund_processed_at);
                }
            }
        }

        $credit = $r->refund_method === 'store_credit'
            ? ['account' => 'customer_credit', 'credit' => $r->refund_amount]
            : ($order && (float) ($order->balance_due ?? 0) > 0
                ? ['account' => 'receivable', 'credit' => $r->refund_amount]
                : ['account' => ChartOfAccounts::paymentMethodKey($r->refund_method), 'credit' => $r->refund_amount]);

        return [
            ['account' => 'sales_returns', 'debit' => round($goodsValue - $vat, 2)],
            ['account' => 'vat_output', 'debit' => $vat],
            $credit,
            ['account' => 'other_income', 'credit' => $r->restocking_fee],
            ['account' => 'inventory', 'debit' => $restock],
            ['account' => 'cogs', 'credit' => $restock],
        ];
    }

    private function creditNoteLines(CreditNote $n): array
    {
        $order = $n->order;
        $taxRatio = $order && (float) $order->total_amount > 0 ? (float) $order->tax / (float) $order->total_amount : 0.0;
        $vat = round((float) $n->amount * $taxRatio, 2);

        return [
            ['account' => 'sales_returns', 'debit' => round((float) $n->amount - $vat, 2)],
            ['account' => 'vat_output', 'debit' => $vat],
            ['account' => 'receivable', 'credit' => $n->amount, 'party' => ['type' => 'customer', 'id' => $order?->customer_id]],
        ];
    }

    // --- in-memory stock replay ---

    private function stockIn(Product $product, float $qty, float $unitCost, $source, $date = null): float
    {
        $pos = $this->stock[$product->id] ?? ['qty' => 0.0, 'value' => 0.0, 'rate' => 0.0];
        $value = round($qty * $unitCost, 2);
        $qtyAfter = round($pos['qty'] + $qty, 3);
        $valueAfter = round($pos['value'] + $value, 2);
        $rate = $qtyAfter > 0 ? $valueAfter / $qtyAfter : $unitCost;

        $this->writeStockRow($product, $qty, $rate, $value, $qtyAfter, $valueAfter, $source, $date);

        return $value;
    }

    private function stockOut(Product $product, float $qty, $source, ?float $knownCost = null, $date = null): float
    {
        $pos = $this->stock[$product->id] ?? ['qty' => 0.0, 'value' => 0.0, 'rate' => 0.0];
        $value = $knownCost !== null ? round($knownCost, 2) : round($qty * $pos['rate'], 2);
        $qtyAfter = round($pos['qty'] - $qty, 3);
        $valueAfter = round($pos['value'] - $value, 2);

        if ($qtyAfter < -0.0005) {
            $this->warnings["oversold"] = true; // flag once
        }

        $this->writeStockRow($product, -$qty, $pos['rate'], -$value, $qtyAfter, $valueAfter, $source, $date);

        return $value;
    }

    private function writeStockRow(Product $product, float $qty, float $rate, float $value, float $qtyAfter, float $valueAfter, $source, $date = null): void
    {
        $batchId = $this->currentBatchId;
        StockLedgerEntry::create([
            'business_id' => $product->business_id,
            'product_id' => $product->id,
            'posting_date' => \Carbon\Carbon::parse($date ?? $source->created_at ?? now())->toDateString(),
            'qty_change' => $qty,
            'valuation_rate' => round($rate, 4),
            'value_change' => $value,
            'qty_after' => $qtyAfter,
            'value_after' => $valueAfter,
            'source_type' => is_object($source) ? $source->getMorphClass() : null,
            'source_id' => is_object($source) ? $source->getKey() : null,
            'batch_id' => $batchId,
        ]);
        $this->stock[$product->id] = ['qty' => $qtyAfter, 'value' => $valueAfter, 'rate' => $rate];
    }

    private string $currentBatchId;

    // --- commit / discard ---

    /** Merge the draft batch into the real books, then reconcile stock to the physical count. */
    public function commit(string $businessId, string $batchId, array $openingBalances): void
    {
        DB::transaction(function () use ($businessId, $batchId, $openingBalances) {
            JournalEntry::where('business_id', $businessId)->where('batch_id', $batchId)
                ->update(['status' => JournalEntry::POSTED, 'batch_id' => null]);

            // Reconcile replayed stock with the physical count, then release SLEs into the live chain
            $products = StockLedgerEntry::where('business_id', $businessId)->where('batch_id', $batchId)
                ->selectRaw('product_id')->groupBy('product_id')->pluck('product_id');

            foreach ($products as $productId) {
                $last = StockLedgerEntry::where('product_id', $productId)->where('batch_id', $batchId)->orderByDesc('id')->first();
                $product = Product::find($productId);
                if (!$last || !$product) {
                    continue;
                }
                $actualQty = (float) $product->stock_quantity;
                $driftQty = round($actualQty - (float) $last->qty_after, 3);
                if (abs($driftQty) >= 0.0005 || abs((float) $last->value_after - $actualQty * (float) $last->valuation_rate) > 1) {
                    $this->warnings[] = "Stock count for '{$product->name}' differed from the books; adjusted by {$driftQty}.";
                }
            }

            StockLedgerEntry::where('business_id', $businessId)->where('batch_id', $batchId)->update(['batch_id' => null]);

            // Products never touched by history still hold stock — book it as opening
            Product::where('business_id', $businessId)->where('stock_quantity', '>', 0)
                ->whereDoesntHave('stockLedgerEntries')->get()
                ->each(fn (Product $p) => app(StockValuation::class)->position($p));

            foreach ($openingBalances as $accountKey => $amount) {
                $amount = (float) $amount;
                if ($amount <= 0) {
                    continue;
                }
                $this->posting->post($businessId, [
                    'date' => now(),
                    'description' => 'Opening balance: '.ucfirst(str_replace('_', ' ', $accountKey)),
                    'lines' => [
                        ['account' => $accountKey, 'debit' => $amount],
                        ['account' => 'opening_balance', 'credit' => $amount],
                    ],
                ]);
            }

            \App\Models\AccountingSetting::for($businessId)->update([
                'setup_mode' => 'backfill',
                'setup_completed_at' => now(),
                'ledger_start_date' => JournalEntry::where('business_id', $businessId)->min('posting_date'),
            ]);
        });
    }

    public function discard(string $businessId, string $batchId): void
    {
        JournalEntry::where('business_id', $businessId)->where('batch_id', $batchId)->draft()->delete();
        StockLedgerEntry::where('business_id', $businessId)->where('batch_id', $batchId)->delete();
    }
}
