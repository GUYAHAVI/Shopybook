<?php

namespace Tests\Feature\Accounting;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Cost;
use App\Services\Accounting\Ledger;
use App\Services\Accounting\ReportService;

class ReportServiceTest extends AccountingTestCase
{
    private Ledger $ledger;
    private ReportService $reports;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ledger = app(Ledger::class);
        $this->reports = app(ReportService::class);
    }

    private function sell(Product $product, float $qty, float $total, float $tax, ?int $customerId = null, ?string $date = '2026-10-05'): Order
    {
        $order = Order::create([
            'business_id' => $this->businessId, 'customer_id' => $customerId,
            'order_number' => 'ORD-'.uniqid(), 'subtotal' => $total - $tax,
            'tax' => $tax, 'total_amount' => $total, 'balance_due' => $total,
        ]);
        // created_at isn't mass-assignable — set it directly so the posted
        // journal lands on the intended date regardless of when tests run.
        $order->created_at = $date.' 10:00:00';
        $order->save();
        [$unitCost, $costAmount] = $this->ledger->issueStockForSale($product, $qty, $order);
        $order->items()->create([
            'product_id' => $product->id, 'quantity' => $qty, 'stock_qty' => $qty,
            'unit_cost' => $unitCost, 'cost_amount' => $costAmount,
            'price' => $total / $qty, 'total' => $total,
        ]);
        $product->decrement('stock_quantity', $qty);
        $this->ledger->postSale($order);

        return $order;
    }

    /** A small trading month: sell, part-pay, buy stock, pay rent. */
    private function scenario(): Order
    {
        // Product::created posts opening stock at now() — suppress it so the
        // opening entry can be dated inside the report window like everything else.
        $product = Product::withoutEvents(fn () => Product::create([
            'business_id' => $this->businessId, 'name' => 'Flour',
            'price' => 116, 'cost_price' => 50, 'stock_quantity' => 20,
        ]));
        $product->created_at = '2026-10-01';
        $product->save();
        $this->ledger->postOpeningStock($product);

        $order = $this->sell($product, 2, total: 232, tax: 32, customerId: 11);

        $payment = $order->payments()->create([
            'business_id' => $this->businessId, 'payment_number' => 'PAY-1',
            'payment_method' => 'mpesa', 'amount' => 100,
        ]);
        // payment_date isn't mass-assignable either — same reason as above.
        $payment->payment_date = '2026-10-05';
        $payment->save();
        $this->ledger->postPayment($payment);

        $cost = Cost::create(['business_id' => $this->businessId, 'type' => 'rent', 'amount' => 5000, 'date' => '2026-10-10']);
        $this->ledger->postCost($cost);

        return $order;
    }

    public function test_profit_loss_splits_vat_cogs_and_expenses(): void
    {
        $this->scenario();
        $pl = $this->reports->profitLoss($this->businessId, '2026-10-01', '2026-10-31');

        $this->assertSame(200.0, $pl['net_sales']);      // 232 gross - 32 VAT
        $this->assertSame(100.0, $pl['cogs_total']);     // 2 x 50
        $this->assertSame(100.0, $pl['gross_profit']);
        $this->assertSame(5000.0, $pl['expenses_total']);
        $this->assertSame(-4900.0, $pl['net_profit']);
    }

    public function test_balance_sheet_balances(): void
    {
        $this->scenario();
        $bs = $this->reports->balanceSheet($this->businessId, '2026-10-31');

        $this->assertSame(0.0, $bs['check']);            // Assets = Liabilities + Equity
        $this->assertSame(-4900.0, $bs['net_profit']);
        // Assets: cash -5000, mpesa +100, receivable +132, inventory 900 (20x50 -100 cogs)
        $this->assertSame(-3868.0, $bs['assets_total']);
        // Liabilities: VAT output 32
        $this->assertSame(32.0, $bs['liabilities_total']);
    }

    public function test_trial_balance_totals_match(): void
    {
        $this->scenario();
        $tb = $this->reports->trialBalance($this->businessId);

        $this->assertSame($tb['total_debits'], $tb['total_credits']);
        $this->assertGreaterThan(0, $tb['rows']->count());
    }

    public function test_cash_book_tracks_mpesa_and_cash(): void
    {
        $this->scenario();
        $cb = $this->reports->cashBook($this->businessId, '2026-10-01', '2026-10-31');

        $mpesa = $cb['rows']->firstWhere('account.system_key', 'mpesa');
        $cash = $cb['rows']->firstWhere('account.system_key', 'cash');
        $this->assertSame(100.0, $mpesa['in']);
        $this->assertSame(5000.0, $cash['out']);
        $this->assertSame(-5000.0, $cash['closing']);
    }

    public function test_aging_groups_by_customer(): void
    {
        $this->scenario();
        $rows = $this->reports->aging($this->businessId, 'receivable', '2026-10-31');

        $row = $rows->firstWhere('party.id', '11');
        $this->assertNotNull($row);
        $this->assertSame(132.0, $row['total']);   // 232 invoiced - 100 paid
        $this->assertSame(132.0, $row['current']); // 26 days old → 0-30 bucket
    }

    public function test_vat_summary(): void
    {
        $this->scenario();
        $vat = $this->reports->vatSummary($this->businessId, '2026-10-01', '2026-10-31');
        $this->assertSame(32.0, $vat['output_vat']);
        $this->assertSame(0.0, $vat['input_vat']);
        $this->assertSame(32.0, $vat['payable']);
    }

    public function test_statement_running_balance_and_dates(): void
    {
        $this->scenario();
        $cash = $this->ledger->account($this->businessId, 'cash');
        $st = $this->reports->accountStatement($this->businessId, $cash->id, '2026-10-01', '2026-10-31');

        $this->assertSame(-5000.0, $st['closing']);
        $this->assertSame(-5000.0, $st['lines']->last()->running_balance);
        $this->assertTrue($st['lines']->contains('description', 'Rent expense'));
    }
}
