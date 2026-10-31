<?php

namespace Tests\Feature\Accounting;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockReceipt;
use App\Models\Supplier;
use App\Models\SupplierDebt;
use App\Models\SupplierPayment;
use App\Services\Accounting\Ledger;

/**
 * Model-level coverage of the PO receive bookkeeping the controller drives:
 * stock receipt → ledger valuation → supplier debt → payment.
 */
class PurchaseOrderFlowTest extends AccountingTestCase
{
    private Ledger $ledger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ledger = app(Ledger::class);
    }

    private function poWithItem(Product $product, float $qty, float $cost): array
    {
        $supplier = Supplier::create(['business_id' => $this->businessId, 'name' => 'Test Supplier']);

        $po = PurchaseOrder::create([
            'business_id' => $this->businessId,
            'supplier_id' => $supplier->id,
            'status' => 'sent',
            'order_date' => today(),
            'total_amount' => $qty * $cost,
        ]);

        $item = $po->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity_ordered' => $qty,
            'unit_cost' => $cost,
            'total_cost' => $qty * $cost,
        ]);

        return [$po, $item, $supplier];
    }

    /** Replicates PurchaseOrderController::receive for one line. */
    private function receiveLine(PurchaseOrder $po, $item, float $qty, string $mode = 'credit'): StockReceipt
    {
        $product = $item->product;
        $total = round($qty * (float) $item->unit_cost, 2);

        $receipt = StockReceipt::create([
            'business_id' => $this->businessId,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'supplier' => $po->supplier->name,
            'supplier_id' => $po->supplier_id,
            'purchase_order_id' => $po->id,
            'purchase_order_item_id' => $item->id,
            'quantity_received' => $qty,
            'unit_cost' => $item->unit_cost,
            'total_cost' => $total,
            'receipt_date' => today(),
            'payment_mode' => $mode,
        ]);

        $this->ledger->receiveStock($receipt, $product);

        if ($mode === 'credit') {
            SupplierDebt::create([
                'business_id' => $this->businessId,
                'supplier_id' => $po->supplier_id,
                'stock_receipt_id' => $receipt->id,
                'purchase_order_id' => $po->id,
                'supplier_name' => $po->supplier->name,
                'reference_number' => $receipt->receipt_number,
                'total_amount' => $total,
                'amount_paid' => 0,
                'balance' => $total,
                'due_date' => today()->addDays(30),
                'status' => 'pending',
            ]);
        }

        $product->increment('stock_quantity', $qty);
        $item->increment('quantity_received', $qty);
        $po->refreshStatus();

        return $receipt;
    }

    public function test_partial_credit_receipt_posts_payable_and_updates_status(): void
    {
        $product = Product::create([
            'business_id' => $this->businessId, 'name' => 'Widget',
            'price' => 150, 'cost_price' => 100, 'stock_quantity' => 0,
        ]);
        [$po, $item] = $this->poWithItem($product, 10, 100);

        $receipt = $this->receiveLine($po, $item, 4);

        $po->refresh();
        $this->assertSame('partially_received', $po->status);
        $this->assertSame(4.0, $po->items->first()->quantity_received);
        $this->assertSame($po->id, $receipt->purchase_order_id);

        // Dr Inventory / Cr Payable (party = supplier)
        $entry = \App\Models\JournalEntry::where('source_type', StockReceipt::class)->where('source_id', $receipt->id)->first();
        $this->assertNotNull($entry);
        $this->assertEquals(400, $entry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'inventory'))->first()->debit);
        $payable = $entry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'payable'))->first();
        $this->assertEquals(400, $payable->credit);
        $this->assertSame('supplier', $payable->party_type);

        // Bill created and linked
        $debt = SupplierDebt::first();
        $this->assertSame($po->id, $debt->purchase_order_id);
        $this->assertSame($receipt->id, $debt->stock_receipt_id);
        $this->assertEquals(400, $debt->balance);
    }

    public function test_full_receipt_marks_po_received(): void
    {
        $product = Product::create([
            'business_id' => $this->businessId, 'name' => 'Widget',
            'price' => 150, 'cost_price' => 100, 'stock_quantity' => 0,
        ]);
        [$po, $item] = $this->poWithItem($product, 5, 100);

        $this->receiveLine($po, $item, 5);

        $po->refresh();
        $this->assertSame('received', $po->status);
        $this->assertFalse($po->receivable);
        $this->assertSame(5.0, $product->fresh()->stock_quantity);
    }

    public function test_paying_the_bill_posts_dr_payable_cr_cash(): void
    {
        $product = Product::create([
            'business_id' => $this->businessId, 'name' => 'Widget',
            'price' => 150, 'cost_price' => 100, 'stock_quantity' => 0,
        ]);
        [$po, $item] = $this->poWithItem($product, 5, 100);
        $this->receiveLine($po, $item, 5);

        $debt = SupplierDebt::first();
        $payment = SupplierPayment::create([
            'business_id' => $this->businessId,
            'supplier_debt_id' => $debt->id,
            'amount' => 500,
            'payment_method' => 'cash',
            'paid_at' => today(),
        ]);

        $entry = $this->ledger->postSupplierPayment($payment);
        $this->assertNotNull($entry);

        $dr = $entry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'payable'))->first();
        $cr = $entry->lines()->whereHas('account', fn ($q) => $q->where('system_key', 'cash'))->first();
        $this->assertEquals(500, $dr->debit);
        $this->assertEquals(500, $cr->credit);
    }
}
