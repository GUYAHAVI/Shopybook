<?php

namespace Tests\Feature\Accounting;

use App\Models\AccountingSetting;
use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Services\Accounting\Ledger;

/**
 * Model-level coverage of the quote→sale bookkeeping QuotationController::convert drives:
 * order + stock issue → postSale/postPayment → customer debt → quote marked converted.
 */
class QuotationFlowTest extends AccountingTestCase
{
    private Ledger $ledger;
    private \App\Models\Business $business;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ledger = app(Ledger::class);
        $this->business = \App\Models\Business::find($this->businessId);
    }

    private function quoteWithItem(Product $product, float $qty, float $price, ?Customer $customer = null): Quotation
    {
        $customer ??= Customer::create(['business_id' => $this->businessId, 'name' => 'Test Customer', 'phone' => '0712000000']);

        $quote = Quotation::create([
            'business_id'  => $this->businessId,
            'customer_id'  => $customer->id,
            'status'       => 'accepted',
            'quote_date'   => today(),
            'subtotal'     => $qty * $price,
            'total_amount' => $qty * $price,
        ]);

        $quote->items()->create([
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'quantity'     => $qty,
            'unit_price'   => $price,
            'total'        => $qty * $price,
        ]);

        return $quote->fresh('items.product');
    }

    /** Replicates QuotationController::convert for the given payment outcome. */
    private function convertQuote(Quotation $quote, string $status, float $amountPaid = 0): Order
    {
        $customer = $quote->customer;
        $products = Product::whereIn('id', $quote->items->pluck('product_id'))->get()->keyBy('id');

        $taxResult = (new \App\Services\TaxCalculator())
            ->calculate(
                $quote->items->map(fn ($i) => [
                    'product'    => $products->get($i->product_id),
                    'product_id' => $i->product_id,
                    'quantity'   => $i->quantity,
                    'price'      => $i->unit_price,
                ]),
                $this->business->getTaxSettingsOrCreate()
            );

        $total      = (float) $taxResult['total'];
        $amountPaid = $status === 'paid' ? $total : ($status === 'unpaid' ? 0.0 : min($amountPaid, $total));
        $balanceDue = round($total - $amountPaid, 2);
        $final      = $balanceDue <= 0 ? 'paid' : ($amountPaid > 0 ? 'partial' : 'unpaid');

        $order = Order::create([
            'business_id'    => $this->businessId,
            'customer_id'    => $customer->id,
            'status'         => 'completed',
            'payment_status' => $final,
            'payment_method' => 'cash',
            'subtotal'       => $taxResult['subtotal'],
            'tax'            => $taxResult['tax'],
            'total_amount'   => $total,
            'amount_paid'    => $amountPaid,
            'balance_due'    => $balanceDue,
        ]);

        if ($final !== 'paid') {
            $order->invoice_number = AccountingSetting::for($this->businessId)->nextInvoiceNumber();
            $order->save();
        }

        if ($balanceDue > 0) {
            CustomerDebt::create([
                'business_id'  => $this->businessId,
                'customer_id'  => $customer->id,
                'order_id'     => $order->id,
                'total_amount' => $total,
                'amount_paid'  => $amountPaid,
                'balance'      => $balanceDue,
                'due_date'     => today()->addDays(30),
                'status'       => $final === 'unpaid' ? 'pending' : 'partial',
            ]);
        }

        if ($amountPaid > 0) {
            $payment = Payment::create([
                'business_id'    => $this->businessId,
                'order_id'       => $order->id,
                'payment_number' => Payment::generatePaymentNumber(),
                'amount'         => $amountPaid,
                'payment_method' => 'cash',
                'payment_date'   => now(),
            ]);
        }

        foreach ($quote->items as $item) {
            $product = $products->get($item->product_id);
            $qty = (float) $item->quantity;
            [$unitCost, $costAmount] = $this->ledger->issueStockForSale($product, $qty, $order);

            $order->items()->create([
                'product_id'  => $product->id,
                'quantity'    => $qty,
                'stock_qty'   => $qty,
                'unit_cost'   => $unitCost,
                'cost_amount' => $costAmount,
                'price'       => $item->unit_price,
                'total'       => $item->total,
            ]);

            $product->stock_quantity -= $qty;
            $product->save();
        }

        $this->ledger->postSale($order);
        if (!empty($payment)) {
            $this->ledger->postPayment($payment);
        }

        $quote->update(['status' => 'converted', 'order_id' => $order->id]);

        return $order;
    }

    public function test_unpaid_conversion_posts_receivable_sale_and_creates_debt(): void
    {
        $product = Product::create([
            'business_id' => $this->businessId, 'name' => 'Widget',
            'price' => 200, 'cost_price' => 120, 'stock_quantity' => 20,
        ]);
        $quote = $this->quoteWithItem($product, 5, 200);

        $order = $this->convertQuote($quote, 'unpaid');

        $quote->refresh();
        $this->assertSame('converted', $quote->status);
        $this->assertSame($order->id, $quote->order_id);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertNotNull($order->invoice_number);
        $this->assertSame(15.0, $product->fresh()->stock_quantity);

        // AR bill
        $debt = CustomerDebt::first();
        $this->assertSame($order->id, $debt->order_id);
        $this->assertEquals(1000, $debt->balance);
        $this->assertSame('pending', $debt->status);

        // Dr Receivable / Cr Sales + Dr COGS / Cr Inventory
        $entry = JournalEntry::where('source_type', Order::class)->where('source_id', $order->id)->first();
        $this->assertNotNull($entry);
        $receivable = $entry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'receivable'))->first();
        $sales = $entry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'sales'))->first();
        $this->assertEquals(1000, $receivable->debit);
        $this->assertEquals(1000, $sales->credit);
        $this->assertSame('customer', $receivable->party_type);

        $cogs = $entry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'cogs'))->first();
        $inventory = $entry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'inventory'))->first();
        $this->assertEquals(600, $cogs->debit); // 5 × 120 cost
        $this->assertEquals(600, $inventory->credit);
    }

    public function test_paid_conversion_posts_cash_and_creates_no_debt(): void
    {
        $product = Product::create([
            'business_id' => $this->businessId, 'name' => 'Widget',
            'price' => 200, 'cost_price' => 120, 'stock_quantity' => 20,
        ]);
        $quote = $this->quoteWithItem($product, 5, 200);

        $order = $this->convertQuote($quote, 'paid');

        $this->assertSame('paid', $order->payment_status);
        $this->assertNull($order->invoice_number);
        $this->assertSame(0, CustomerDebt::count());
        $this->assertSame(1, Payment::count());

        // Sale posts Dr Receivable / Cr Sales (payment settles it below)
        $entry = JournalEntry::where('source_type', Order::class)->where('source_id', $order->id)->first();
        $receivable = $entry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'receivable'))->first();
        $this->assertEquals(1000, $receivable->debit);

        // Payment posts Dr Cash / Cr Receivable
        $payment = Payment::first();
        $payEntry = JournalEntry::where('source_type', Payment::class)->where('source_id', $payment->id)->first();
        $this->assertNotNull($payEntry);
        $cash = $payEntry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'cash'))->first();
        $recvCr = $payEntry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'receivable'))->first();
        $this->assertEquals(1000, $cash->debit);
        $this->assertEquals(1000, $recvCr->credit);
    }

    public function test_partial_conversion_marks_debt_partial_and_posts_payment(): void
    {
        $product = Product::create([
            'business_id' => $this->businessId, 'name' => 'Widget',
            'price' => 200, 'cost_price' => 120, 'stock_quantity' => 20,
        ]);
        $quote = $this->quoteWithItem($product, 5, 200);

        $order = $this->convertQuote($quote, 'partial', 400);

        $this->assertSame('partial', $order->payment_status);
        $this->assertEquals(600, $order->balance_due);

        $debt = CustomerDebt::first();
        $this->assertSame('partial', $debt->status);
        $this->assertEquals(600, $debt->balance);

        // Payment posts Dr Cash / Cr Receivable
        $payment = Payment::first();
        $entry = JournalEntry::where('source_type', Payment::class)->where('source_id', $payment->id)->first();
        $this->assertNotNull($entry);
        $cash = $entry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'cash'))->first();
        $receivable = $entry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'receivable'))->first();
        $this->assertEquals(400, $cash->debit);
        $this->assertEquals(400, $receivable->credit);
    }

    public function test_prospect_quote_gets_customer_before_conversion(): void
    {
        // A quote for a non-saved prospect has snapshot fields instead of customer_id
        $product = Product::create([
            'business_id' => $this->businessId, 'name' => 'Widget',
            'price' => 200, 'cost_price' => 120, 'stock_quantity' => 20,
        ]);

        $quote = Quotation::create([
            'business_id'  => $this->businessId,
            'customer_name' => 'Walk-up Prospect',
            'status'       => 'sent',
            'quote_date'   => today(),
            'total_amount' => 400,
        ]);
        $quote->items()->create([
            'product_id' => $product->id, 'product_name' => $product->name,
            'quantity' => 2, 'unit_price' => 200, 'total' => 400,
        ]);

        $this->assertTrue($quote->convertible);

        // Conversion creates the customer record first (controller behaviour)
        $customer = Customer::create([
            'business_id' => $this->businessId,
            'name'        => $quote->customer_name,
        ]);
        $quote->update(['customer_id' => $customer->id]);

        $order = $this->convertQuote($quote->fresh('items.product', 'customer'), 'unpaid');
        $this->assertSame($customer->id, $order->customer_id);
        $this->assertSame('converted', $quote->fresh()->status);
    }
}
