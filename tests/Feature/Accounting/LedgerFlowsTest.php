<?php

namespace Tests\Feature\Accounting;

use App\Models\CommissionPayout;
use App\Models\CreditNote;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Payment;
use App\Models\Product;
use App\Models\RentalPayment;
use App\Models\RentalTenant;
use App\Models\SalaryAdvance;
use App\Models\ServiceBooking;
use App\Models\Staff;
use App\Models\StockLedgerEntry;
use App\Services\Accounting\Ledger;

class LedgerFlowsTest extends AccountingTestCase
{
    private Ledger $ledger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ledger = app(Ledger::class);
    }

    private function product(float $stock = 0, ?float $cost = null): Product
    {
        return Product::create([
            'business_id' => $this->businessId,
            'name' => 'Item '.uniqid(),
            'price' => 116,
            'cost_price' => $cost,
            'stock_quantity' => $stock,
        ]);
    }

    private function sell(Product $product, float $qty, float $total, float $tax, ?int $customerId = null): Order
    {
        $order = Order::create([
            'business_id' => $this->businessId,
            'customer_id' => $customerId,
            'order_number' => 'ORD-'.uniqid(),
            'subtotal' => $total - $tax,
            'tax' => $tax,
            'total_amount' => $total,
            'balance_due' => $total,
        ]);

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

    public function test_full_return_refunds_reverses_vat_and_restock(): void
    {
        $product = $this->product(stock: 10, cost: 50);
        $order = $this->sell($product, 4, total: 464, tax: 64, customerId: 9);
        $order->update(['balance_due' => 0]);

        $return = OrderReturn::create([
            'business_id' => $this->businessId, 'order_id' => $order->id, 'customer_id' => 9,
            'return_type' => 'full', 'status' => 'approved', 'original_amount' => 464,
            'refund_amount' => 464, 'return_to_stock' => true, 'refund_method' => 'cash',
        ]);

        $restock = $this->ledger->restockReturnGoods($return);
        $return->update(['refund_processed_at' => now()]);
        $this->ledger->postReturn($return, $restock);

        $this->assertSame(400.0, $this->balance('sales_returns'));   // 464 gross - 64 VAT
        $this->assertSame(0.0, $this->balance('vat_output'));
        $this->assertSame(-464.0, $this->balance('cash'));
        $this->assertSame(200.0, $restock);                          // 4 x 50 back at cost
        $this->assertSame(0.0, $this->balance('cogs'));
        $this->assertSame(500.0, $this->balance('inventory'));
        $this->assertSame(10.0, (float) $product->fresh()->stock_quantity);
        $this->assertLedgerBalances();
    }

    public function test_store_credit_return_and_unpaid_return_destinations(): void
    {
        $product = $this->product(stock: 10, cost: 20);

        // Unpaid order: refund reduces the receivable, no money moves
        $order = $this->sell($product, 2, total: 100, tax: 0, customerId: 4);
        $return = OrderReturn::create([
            'business_id' => $this->businessId, 'order_id' => $order->id, 'customer_id' => 4,
            'return_type' => 'partial', 'status' => 'approved', 'refund_amount' => 50,
            'return_to_stock' => false, 'refund_method' => 'cash',
        ]);
        $this->ledger->postReturn($return, 0);
        $this->assertSame(50.0, $this->balance('receivable'));
        $this->assertSame(0.0, $this->balance('cash'));

        // Paid order refunded as store credit: liability, not cash out
        $order->update(['balance_due' => 0]);
        $return2 = OrderReturn::create([
            'business_id' => $this->businessId, 'order_id' => $order->id, 'customer_id' => 4,
            'return_type' => 'partial', 'status' => 'approved', 'refund_amount' => 30,
            'return_to_stock' => false, 'refund_method' => 'store_credit',
        ]);
        $this->ledger->postReturn($return2, 0);
        $this->assertSame(30.0, $this->balance('customer_credit'));
        $this->assertSame(0.0, $this->balance('cash'));
    }

    public function test_credit_note_reduces_receivable_and_vat(): void
    {
        $order = $this->sell($this->product(stock: 5, cost: 10), 2, total: 232, tax: 32, customerId: 6);

        $this->ledger->postCreditNote(CreditNote::create([
            'order_id' => $order->id, 'credit_note_number' => 'CN-1', 'amount' => 116,
        ]));

        $this->assertSame(116.0, $this->balance('receivable')); // 232 - 116
        $this->assertSame(100.0, $this->balance('sales_returns'));
        $this->assertSame(16.0, $this->balance('vat_output')); // 32 - 16
        $this->assertLedgerBalances();
    }

    public function test_cancelling_a_sale_reverses_everything_and_restock(): void
    {
        $product = $this->product(stock: 8, cost: 40);
        $order = $this->sell($product, 3, total: 300, tax: 0, customerId: 2);
        $payment = $order->payments()->create([
            'business_id' => $this->businessId, 'payment_method' => 'mpesa', 'amount' => 300,
        ]);
        $this->ledger->postPayment($payment);

        $this->ledger->cancelSale($order);

        $this->assertSame(0.0, $this->balance('receivable'));
        $this->assertSame(0.0, $this->balance('sales'));
        $this->assertSame(0.0, $this->balance('mpesa'));
        $this->assertSame(8.0, (float) $product->fresh()->stock_quantity);
        $this->assertSame(320.0, $this->balance('inventory')); // opening 8 x 40 restored
        $this->assertLedgerBalances();
    }

    public function test_service_booking_posts_only_when_paid(): void
    {
        $pending = ServiceBooking::create([
            'business_id' => $this->businessId, 'final_amount' => 800,
            'payment_status' => 'pending', 'payment_method' => 'cash',
        ]);
        $this->assertNull($this->ledger->postServiceBooking($pending));

        $paid = ServiceBooking::create([
            'business_id' => $this->businessId, 'final_amount' => 800,
            'payment_status' => 'paid', 'payment_method' => 'bank_transfer',
        ]);
        $this->ledger->postServiceBooking($paid);

        $this->assertSame(800.0, $this->balance('service_income'));
        $this->assertSame(800.0, $this->balance('bank'));
    }

    public function test_rent_payment_and_unmark(): void
    {
        $tenant = RentalTenant::create(['business_id' => $this->businessId, 'name' => 'Asha']);
        $payment = RentalPayment::create([
            'business_id' => $this->businessId, 'rental_tenant_id' => $tenant->id,
            'period' => '2026-10', 'amount' => 12000, 'method' => 'mpesa', 'status' => 'paid',
        ]);
        $this->ledger->postRentalPayment($payment);
        $this->assertSame(12000.0, $this->balance('rental_income'));
        $this->assertSame(12000.0, $this->balance('mpesa'));

        $payment->update(['status' => 'reversed']);
        $this->ledger->reverse($payment);
        $this->assertSame(0.0, $this->balance('rental_income'));
        $this->assertSame(0.0, $this->balance('mpesa'));
    }

    public function test_salary_advance_and_instalment_deductions(): void
    {
        $staff = Staff::create(['business_id' => $this->businessId, 'name' => 'Wanjiru', 'salary' => 30000]);
        $advance = SalaryAdvance::create([
            'business_id' => $this->businessId, 'staff_id' => $staff->id,
            'amount' => 5000, 'remaining_balance' => 5000,
        ]);
        $this->ledger->postSalaryAdvance($advance);
        $this->assertSame(5000.0, $this->balance('staff_advances'));
        $this->assertSame(-5000.0, $this->balance('cash'));

        // Two instalment deductions reduce what is owed to staff (negative = prepaid here
        // because no salary cost has been booked yet)
        $this->ledger->postAdvanceDeduction($advance, 2000);
        $this->ledger->postAdvanceDeduction($advance, 3000);
        $this->assertSame(0.0, $this->balance('staff_advances'));
        $this->assertSame(-5000.0, $this->balance('salaries_payable'));
    }

    public function test_commission_payout(): void
    {
        $staff = Staff::create(['business_id' => $this->businessId, 'name' => 'Otieno']);
        $this->ledger->postCommission(CommissionPayout::create([
            'business_id' => $this->businessId, 'staff_id' => $staff->id, 'amount' => 1500,
        ]));
        $this->assertSame(1500.0, $this->balance('expense_commission'));
        $this->assertSame(-1500.0, $this->balance('cash'));
    }

    public function test_stock_adjustment_both_directions(): void
    {
        $product = $this->product(stock: 20, cost: 30); // no SLE yet
        $this->ledger->postStockAdjustment($product, 25); // count found 25
        $product->update(['stock_quantity' => 25]);

        // Opening 600 (20 x 30) + adjustment +150 (5 x 30)
        $this->assertSame(600.0, $this->balance('opening_balance'));
        $this->assertSame(750.0, $this->balance('inventory'));
        $this->assertSame(-150.0, $this->balance('stock_adjustment'));

        $this->ledger->postStockAdjustment($product, 22); // shrinkage: 3 units lost
        $product->update(['stock_quantity' => 22]);
        $this->assertSame(-60.0, $this->balance('stock_adjustment')); // +150 found, -90 lost
        $this->assertSame(660.0, $this->balance('inventory'));
        $this->assertLedgerBalances();
    }

    public function test_owner_money_movements(): void
    {
        $this->ledger->moneyAccounts($this->businessId); // ensures the chart exists
        $mpesa = \App\Models\Account::where('business_id', $this->businessId)->where('system_key', 'mpesa')->first();
        $bank = \App\Models\Account::where('business_id', $this->businessId)->where('system_key', 'bank')->first();

        $this->ledger->postOwnerInjection($this->businessId, $mpesa, 10000);
        $this->assertSame(10000.0, $this->balance('mpesa'));
        $this->assertSame(10000.0, $this->balance('capital'));

        $this->ledger->postTransfer($this->businessId, $mpesa, $bank, 5000, charge: 20);
        $this->assertSame(5000.0, $this->balance('bank'));
        $this->assertSame(4980.0, $this->balance('mpesa'));
        $this->assertSame(20.0, $this->balance('expense_charges'));

        $this->ledger->postOwnerDrawing($this->businessId, $bank, 2000);
        $this->assertSame(2000.0, $this->balance('drawings'));
        $this->assertSame(3000.0, $this->balance('bank'));
        $this->assertLedgerBalances();
    }
}
