<?php

namespace Tests\Feature\Accounting;

use App\Models\AccountingSetting;
use App\Models\Cost;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockLedgerEntry;
use App\Services\Accounting\BackfillService;
use App\Services\Accounting\ReportService;

class BackfillTest extends AccountingTestCase
{
    /** Seed "history" as plain operational rows, then rebuild the books from it. */
    private function history(): void
    {
        $product = Product::create(['business_id' => $this->businessId, 'name' => 'Rice', 'price' => 116, 'cost_price' => 50, 'stock_quantity' => 18]);

        $order = Order::create(['business_id' => $this->businessId, 'order_number' => 'ORD-OLD', 'status' => 'completed', 'subtotal' => 200, 'tax' => 32, 'total_amount' => 232, 'balance_due' => 232, 'customer_id' => 5]);
        $order->items()->create(['product_id' => $product->id, 'quantity' => 2, 'stock_qty' => 2, 'price' => 116, 'total' => 232]); // no cost_amount — historical

        Payment::create(['order_id' => $order->id, 'business_id' => $this->businessId, 'payment_number' => 'PAY-OLD', 'payment_method' => 'mpesa', 'amount' => 100, 'payment_date' => '2026-09-01']);

        Cost::create(['business_id' => $this->businessId, 'type' => 'rent', 'amount' => 3000, 'date' => '2026-09-05']);
    }

    public function test_backfill_builds_a_draft_then_commits(): void
    {
        $this->history();
        // Only the product's opening stock exists — booked by the Product::created hook.
        // Everything else stays out of the books until backfill commits.
        $this->assertSame(1, JournalEntry::where('status', 'posted')->count());

        $backfill = app(BackfillService::class);
        $result = $backfill->run($this->businessId);
        $batch = $result['batch_id'];

        $draftCount = JournalEntry::where('batch_id', $batch)->draft()->count();
        $this->assertGreaterThanOrEqual(3, $draftCount); // sale, payment, cost

        // Drafts don't affect the real books
        $this->assertSame(900.0, $this->balance('opening_balance')); // only product opening (18 x 50)
        $this->assertSame(0.0, $this->balance('sales'));

        $backfill->commit($this->businessId, $batch, ['cash' => 5000]);

        $this->assertSame(0, JournalEntry::whereNotNull('batch_id')->count());
        $this->assertSame(200.0, $this->balance('sales'));
        $this->assertSame(100.0, $this->balance('mpesa'));
        $this->assertSame(2000.0, $this->balance('cash')); // 5000 opening - 3000 replayed rent
        $this->assertSame(132.0, $this->balance('receivable'));
        $this->assertSame(3000.0, $this->balance('expense_rent'));
        $this->assertSame(0.0, app(ReportService::class)->balanceSheet($this->businessId, now()->toDateString())['check']);

        $settings = AccountingSetting::for($this->businessId);
        $this->assertSame('backfill', $settings->setup_mode);
        $this->assertNotNull($settings->setup_completed_at);
    }

    public function test_discard_removes_everything(): void
    {
        $this->history();
        $result = app(BackfillService::class)->run($this->businessId);
        app(BackfillService::class)->discard($this->businessId, $result['batch_id']);

        $this->assertSame(0, JournalEntry::whereNotNull('batch_id')->count());
        $this->assertSame(0, StockLedgerEntry::whereNotNull('batch_id')->count());
    }
}
