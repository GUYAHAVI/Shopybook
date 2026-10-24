<?php

namespace Tests\Feature\Accounting;

use App\Models\Cost;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockLedgerEntry;
use App\Models\StockReceipt;
use App\Models\SupplierDebt;
use App\Models\SupplierPayment;
use App\Services\Accounting\Ledger;
use App\Services\Accounting\PostingService;

class LedgerTest extends AccountingTestCase
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
            'name' => 'Sugar 1kg '.uniqid(),
            'price' => 116,
            'cost_price' => $cost,
            'stock_quantity' => $stock,
        ]);
    }

    /** Mirrors SalesController::createOrder: issue stock, create items, reduce stock, post sale. */
    private function sell(Product $product, float $qty, float $total, float $tax, ?int $customerId = null): Order
    {
        $order = Order::create([
            'business_id' => $this->businessId,
            'customer_id' => $customerId,
            'order_number' => 'ORD-'.uniqid(),
            'subtotal' => $total - $tax,
            'tax' => $tax,
            'total_amount' => $total,
        ]);

        [$unitCost, $costAmount] = $this->ledger->issueStockForSale($product, $qty, $order);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => $qty,
            'stock_qty' => $qty,
            'unit_cost' => $unitCost,
            'cost_amount' => $costAmount,
            'price' => $total / $qty,
            'total' => $total,
        ]);
        $product->decrement('stock_quantity', $qty);
        $this->ledger->postSale($order);

        return $order;
    }

    private function receive(Product $product, float $qty, float $unitCost, string $mode = 'paid', ?int $paidFrom = null): StockReceipt
    {
        $receipt = StockReceipt::create([
            'business_id' => $this->businessId,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'supplier' => 'Mumias Distributors',
            'quantity_received' => $qty,
            'unit_cost' => $unitCost,
            'total_cost' => $qty * $unitCost,
            'payment_mode' => $mode,
            'paid_from_account_id' => $paidFrom,
            'receipt_date' => '2026-10-02',
        ]);
        $this->ledger->receiveStock($receipt, $product);
        $product->increment('stock_quantity', $qty);

        return $receipt;
    }

    private function stockValue(Product $product): float
    {
        return (float) StockLedgerEntry::where('product_id', $product->id)->orderByDesc('id')->value('value_after');
    }

    public function test_sale_of_existing_stock_books_revenue_vat_and_cost(): void
    {
        $product = $this->product(stock: 10, cost: 60);

        $this->sell($product, 2, total: 232, tax: 32, customerId: 7);

        // Existing stock was brought in as opening inventory (10 x 60), then 2 sold at cost 60.
        $this->assertSame(600.0, $this->balance('opening_balance'));
        $this->assertSame(232.0, $this->balance('receivable'));
        $this->assertSame(200.0, $this->balance('sales'));
        $this->assertSame(32.0, $this->balance('vat_output'));
        $this->assertSame(120.0, $this->balance('cogs'));
        $this->assertSame(480.0, $this->balance('inventory'));
        $this->assertSame(480.0, $this->stockValue($product));
        $this->assertLedgerBalances();
    }

    public function test_payment_clears_receivable_into_the_right_money_account(): void
    {
        $order = $this->sell($this->product(stock: 5, cost: 50), 1, total: 100, tax: 0, customerId: 3);

        $payment = Payment::create([
            'order_id' => $order->id, 'business_id' => $this->businessId, 'payment_number' => 'PAY-1',
            'payment_method' => 'mpesa', 'amount' => 100, 'payment_date' => '2026-10-03',
        ]);
        $this->ledger->postPayment($payment);

        $this->assertSame(0.0, $this->balance('receivable'));
        $this->assertSame(100.0, $this->balance('mpesa'));
        $this->assertSame(0.0, $this->balance('cash'));
    }

    public function test_unconfirmed_or_failed_gateway_payments_are_not_booked(): void
    {
        $order = $this->sell($this->product(stock: 5, cost: 50), 1, total: 100, tax: 0);

        foreach ([['pending', 'ws_CO_1'], ['failed', 'ws_CO_2'], ['cancelled', null]] as $i => [$status, $txn]) {
            $this->assertNull($this->ledger->postPayment(Payment::create([
                'order_id' => $order->id, 'business_id' => $this->businessId, 'payment_number' => "PAY-{$i}",
                'payment_method' => 'mpesa', 'amount' => 100, 'status' => $status, 'transaction_id' => $txn,
            ])));
        }
        $this->assertSame(100.0, $this->balance('receivable'));
    }

    public function test_moving_average_cost_across_purchases(): void
    {
        $product = $this->product();
        $mpesa = app(PostingService::class)->account($this->businessId, 'mpesa');

        $this->receive($product, 10, 100, 'paid', $mpesa->id);
        $this->receive($product, 10, 120, 'credit');
        $this->assertEqualsWithDelta(110.0, (float) $product->fresh()->valuation_rate, 0.0001);

        $order = $this->sell($product, 5, total: 800, tax: 0);

        $this->assertSame('550.00', $order->items->first()->cost_amount);
        $this->assertSame(-1000.0, $this->balance('mpesa'));
        $this->assertSame(1200.0, $this->balance('payable'));
        $this->assertSame(1650.0, $this->balance('inventory'));
        $this->assertSame(1650.0, $this->stockValue($product));
        $this->assertSame(0.0, $this->balance('opening_balance'), 'New product had no opening stock');
        $this->assertLedgerBalances();
    }

    public function test_selling_all_stock_leaves_no_value_residue(): void
    {
        $product = $this->product();
        $this->receive($product, 3, 10);
        $this->receive($product, 3, 11); // average 10.5
        $this->sell($product, 6, total: 90, tax: 0);

        $this->assertSame(0.0, $this->balance('inventory'));
        $this->assertSame(63.0, $this->balance('cogs'));
    }

    public function test_manual_supplier_bill_and_payment(): void
    {
        $rent = app(PostingService::class)->account($this->businessId, 'expense_rent');
        $debt = SupplierDebt::create([
            'business_id' => $this->businessId, 'supplier_name' => 'Landlord', 'reference_number' => 'INV-9',
            'total_amount' => 3000, 'balance' => 3000, 'expense_account_id' => $rent->id,
        ]);
        $this->ledger->postSupplierDebt($debt);

        $this->assertSame(3000.0, $this->balance('expense_rent'));
        $this->assertSame(3000.0, $this->balance('payable'));

        $payment = SupplierPayment::create([
            'business_id' => $this->businessId, 'supplier_debt_id' => $debt->id, 'amount' => 1000,
            'payment_method' => 'bank_transfer', 'paid_at' => '2026-10-05',
        ]);
        $this->ledger->postSupplierPayment($payment);

        $this->assertSame(2000.0, $this->balance('payable'));
        $this->assertSame(-1000.0, $this->balance('bank'));
    }

    public function test_debt_from_on_credit_stock_receipt_is_not_double_counted(): void
    {
        $receipt = $this->receive($this->product(), 4, 250, 'credit');
        $debt = SupplierDebt::create([
            'business_id' => $this->businessId, 'stock_receipt_id' => $receipt->id, 'supplier_name' => 'Mumias',
            'reference_number' => $receipt->receipt_number, 'total_amount' => 1000, 'balance' => 1000,
        ]);

        $this->assertNull($this->ledger->postSupplierDebt($debt));
        $this->assertSame(1000.0, $this->balance('payable'));
    }

    public function test_cost_is_booked_edited_and_deleted_through_reversals(): void
    {
        $mpesa = app(PostingService::class)->account($this->businessId, 'mpesa');
        $cost = Cost::create(['business_id' => $this->businessId, 'type' => 'utility', 'amount' => 2500, 'date' => '2026-10-04', 'paid_from_account_id' => $mpesa->id]);
        $this->ledger->postCost($cost);

        $this->assertSame(2500.0, $this->balance('expense_utility'));
        $this->assertSame(-2500.0, $this->balance('mpesa'));

        $cost->update(['amount' => 3000, 'paid_from_account_id' => null]);
        $this->ledger->repostCost($cost);
        $this->assertSame(3000.0, $this->balance('expense_utility'));
        $this->assertSame(0.0, $this->balance('mpesa'));
        $this->assertSame(-3000.0, $this->balance('cash'));

        $this->ledger->reverse($cost);
        $this->assertSame(0.0, $this->balance('expense_utility'));
        $this->assertSame(0.0, $this->balance('cash'));
        $this->assertSame(0, JournalEntry::active()->where('source_type', $cost->getMorphClass())->count());
        $this->assertLedgerBalances();
    }

    public function test_returned_converted_items_restock_the_deducted_quantity(): void
    {
        $sqm = new OrderItem(['quantity' => 10, 'sell_unit' => 'sqm', 'material_type' => 'greenhouse_0.2', 'original_quantity' => 10]);
        $this->assertSame(2.0, $sqm->stockQuantity());
        $this->assertSame(1.0, $sqm->stockQuantityFor(5));

        $recorded = new OrderItem(['quantity' => 4, 'stock_qty' => 1.5]);
        $this->assertSame(0.75, $recorded->stockQuantityFor(2));

        $plain = new OrderItem(['quantity' => 3]);
        $this->assertSame(3.0, $plain->stockQuantityFor(99));
    }
}
