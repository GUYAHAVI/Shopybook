<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\StockReceipt;
use App\Models\Supplier;
use App\Models\SupplierDebt;
use App\Services\Accounting\Ledger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PurchaseOrderMail;

class PurchaseOrderController extends Controller
{
    private function businessId(): string
    {
        return auth()->user()->business->id;
    }

    private function scopeToBusiness(PurchaseOrder $po): void
    {
        if ((string) $po->business_id !== $this->businessId()) {
            abort(403, 'Unauthorized access to purchase order.');
        }
    }

    public function index(Request $request)
    {
        $businessId = $this->businessId();

        $query = PurchaseOrder::where('business_id', $businessId)
            ->with('supplier')
            ->withCount('items')
            ->withSum('items', 'quantity_received')
            ->withSum('items', 'quantity_ordered')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        $pos = $query->paginate(20);
        $suppliers = Supplier::where('business_id', $businessId)->orderBy('name')->get(['id', 'name']);

        $stats = [
            'open' => PurchaseOrder::where('business_id', $businessId)->whereIn('status', ['sent', 'partially_received'])->count(),
            'draft' => PurchaseOrder::where('business_id', $businessId)->where('status', 'draft')->count(),
            'open_value' => PurchaseOrder::where('business_id', $businessId)->whereIn('status', ['sent', 'partially_received'])->sum('total_amount'),
            'total' => PurchaseOrder::where('business_id', $businessId)->count(),
        ];

        return view('purchase-orders.index', compact('pos', 'suppliers', 'stats'));
    }

    public function create()
    {
        $business = auth()->user()->business;
        $suppliers = Supplier::where('business_id', $business->id)->orderBy('name')->get();
        $products = Product::where('business_id', $business->id)
            ->orderBy('name')
            ->get(['id', 'name', 'cost_price', 'measurement_unit', 'sku']);

        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $businessId = $this->businessId();

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'send' => 'nullable|boolean',
        ]);

        $supplier = Supplier::where('business_id', $businessId)->findOrFail($validated['supplier_id']);

        $po = DB::transaction(function () use ($validated, $supplier, $businessId, $request) {
            $po = PurchaseOrder::create([
                'business_id' => $businessId,
                'supplier_id' => $supplier->id,
                'status' => $request->boolean('send') ? 'sent' : 'draft',
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
                'total_amount' => 0,
            ]);

            $total = 0;
            foreach ($validated['items'] as $line) {
                $product = Product::where('business_id', $businessId)->findOrFail($line['product_id']);
                $lineTotal = round($line['quantity'] * $line['unit_cost'], 2);
                $total += $lineTotal;

                $po->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity_ordered' => $line['quantity'],
                    'unit_cost' => $line['unit_cost'],
                    'total_cost' => $lineTotal,
                ]);
            }

            $po->total_amount = $total;
            $po->save();

            return $po;
        });

        return redirect()->route('purchase-orders.show', $po)
            ->with('success', "Purchase order {$po->po_number} created" . ($po->status === 'sent' ? ' and marked as sent.' : '.'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $this->scopeToBusiness($purchaseOrder);
        $purchaseOrder->load(['supplier', 'items.product', 'receipts', 'debts.payments']);

        return view('purchase-orders.show', ['po' => $purchaseOrder]);
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->scopeToBusiness($purchaseOrder);
        if (!$purchaseOrder->editable) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only draft purchase orders can be edited.');
        }

        $business = auth()->user()->business;
        $suppliers = Supplier::where('business_id', $business->id)->orderBy('name')->get();
        $products = Product::where('business_id', $business->id)
            ->orderBy('name')
            ->get(['id', 'name', 'cost_price', 'measurement_unit', 'sku']);
        $purchaseOrder->load('items');

        return view('purchase-orders.edit', [
            'po' => $purchaseOrder,
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->scopeToBusiness($purchaseOrder);
        if (!$purchaseOrder->editable) {
            return back()->with('error', 'Only draft purchase orders can be edited.');
        }

        $businessId = $this->businessId();
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $purchaseOrder, $businessId) {
            $purchaseOrder->items()->delete();
            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $total = 0;
            foreach ($validated['items'] as $line) {
                $product = Product::where('business_id', $businessId)->findOrFail($line['product_id']);
                $lineTotal = round($line['quantity'] * $line['unit_cost'], 2);
                $total += $lineTotal;

                $purchaseOrder->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity_ordered' => $line['quantity'],
                    'unit_cost' => $line['unit_cost'],
                    'total_cost' => $lineTotal,
                ]);
            }

            $purchaseOrder->total_amount = $total;
            $purchaseOrder->save();
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', "Purchase order {$purchaseOrder->po_number} updated.");
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->scopeToBusiness($purchaseOrder);
        if ($purchaseOrder->receipts()->exists()) {
            return back()->with('error', 'Cannot delete a purchase order with recorded receipts. Cancel it instead.');
        }

        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order deleted.');
    }

    public function send(PurchaseOrder $purchaseOrder)
    {
        $this->scopeToBusiness($purchaseOrder);
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft purchase orders can be sent.');
        }

        $emailed = $this->emailSupplier($purchaseOrder);

        $purchaseOrder->status = 'sent';
        $purchaseOrder->save();

        if ($emailed) {
            return back()->with('success', "Purchase order {$purchaseOrder->po_number} emailed to {$purchaseOrder->supplier->email}.");
        }

        $reason = $purchaseOrder->supplier?->email
            ? 'the email could not be delivered — check the mail settings and try "Resend Email".'
            : 'the supplier has no email address on record — share the order manually.';
        return back()->with('warning', "Purchase order {$purchaseOrder->po_number} marked as sent, but {$reason}");
    }

    /**
     * Re-email a sent PO to the supplier.
     */
    public function resendEmail(PurchaseOrder $purchaseOrder)
    {
        $this->scopeToBusiness($purchaseOrder);
        if ($purchaseOrder->status === 'draft') {
            return back()->with('error', 'Send the purchase order first.');
        }
        if (!$purchaseOrder->supplier?->email) {
            return back()->with('error', 'This supplier has no email address on record.');
        }

        if ($this->emailSupplier($purchaseOrder)) {
            return back()->with('success', "Purchase order {$purchaseOrder->po_number} re-sent to {$purchaseOrder->supplier->email}.");
        }

        return back()->with('error', 'The email could not be delivered. Check the mail settings and try again.');
    }

    private function emailSupplier(PurchaseOrder $po): bool
    {
        $email = $po->supplier?->email;
        if (!$email) {
            return false;
        }

        try {
            Mail::to($email)->send(new PurchaseOrderMail($po));
            return true;
        } catch (\Throwable $e) {
            Log::error('Purchase order email failed', [
                'po_id' => $po->id,
                'supplier_email' => $email,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        $this->scopeToBusiness($purchaseOrder);
        if ($purchaseOrder->receipts()->exists()) {
            return back()->with('error', 'Cannot cancel a purchase order with recorded receipts.');
        }

        $purchaseOrder->status = 'cancelled';
        $purchaseOrder->save();

        return back()->with('success', "Purchase order {$purchaseOrder->po_number} cancelled.");
    }

    /**
     * Form for receiving goods against this PO.
     */
    public function receiveForm(PurchaseOrder $purchaseOrder, Ledger $ledger)
    {
        $this->scopeToBusiness($purchaseOrder);
        if (!$purchaseOrder->receivable) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'This purchase order cannot receive stock in its current status.');
        }

        $purchaseOrder->load('items.product');
        $moneyAccounts = $ledger->moneyAccounts($this->businessId());

        return view('purchase-orders.receive', ['po' => $purchaseOrder, 'moneyAccounts' => $moneyAccounts]);
    }

    /**
     * Receive quantities against PO lines: stock receipt + ledger + stock + debt per line,
     * all inside one transaction (mirrors ProductsController::processReceive bookkeeping).
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder, Ledger $ledger)
    {
        $this->scopeToBusiness($purchaseOrder);
        if (!$purchaseOrder->receivable) {
            return back()->with('error', 'This purchase order cannot receive stock in its current status.');
        }

        $purchaseOrder->load('items.product');
        $businessId = $this->businessId();

        $request->validate([
            'receipt_date' => 'required|date',
            'payment_mode' => 'required|in:paid,credit',
            'paid_from_account_id' => ['nullable', 'required_if:payment_mode,paid', 'integer', Ledger::moneyAccountRule($businessId)],
            'due_date' => 'nullable|required_if:payment_mode,credit|date',
            'invoice_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.quantity' => 'nullable|numeric|min:0',
        ]);

        $lines = collect($request->input('items', []))
            ->filter(fn ($line) => isset($line['quantity']) && (float) $line['quantity'] > 0);

        if ($lines->isEmpty()) {
            return back()->withErrors(['items' => 'Enter a quantity to receive for at least one line.'])->withInput();
        }

        foreach ($lines as $itemId => $line) {
            $item = $purchaseOrder->items->firstWhere('id', (int) $itemId);
            if (!$item) {
                return back()->withErrors(['items' => 'Invalid line selected.'])->withInput();
            }
            if ((float) $line['quantity'] > $item->remaining) {
                return back()->withErrors([
                    'items' => "Quantity for '{$item->product_name}' exceeds the remaining {$item->remaining} units.",
                ])->withInput();
            }
        }

        try {
            DB::transaction(function () use ($lines, $purchaseOrder, $request, $ledger, $businessId) {
                $onCredit = $request->payment_mode === 'credit';

                foreach ($lines as $itemId => $line) {
                    $item = $purchaseOrder->items->firstWhere('id', (int) $itemId);
                    $product = $item->product;
                    $qty = (float) $line['quantity'];
                    $unitCost = (float) $item->unit_cost;
                    $totalCost = round($qty * $unitCost, 2);

                    $receipt = StockReceipt::create([
                        'business_id' => $businessId,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'supplier' => $purchaseOrder->supplier->name,
                        'supplier_id' => $purchaseOrder->supplier_id,
                        'purchase_order_id' => $purchaseOrder->id,
                        'purchase_order_item_id' => $item->id,
                        'quantity_received' => $qty,
                        'unit_cost' => $unitCost,
                        'total_cost' => $totalCost,
                        'receipt_date' => $request->receipt_date,
                        'invoice_number' => $request->invoice_number,
                        'notes' => $request->notes,
                        'received_by' => auth()->id(),
                        'receipt_type' => 'existing_product',
                        'payment_mode' => $onCredit ? 'credit' : 'paid',
                        'paid_from_account_id' => $onCredit ? null : $request->paid_from_account_id,
                    ]);

                    // Value stock into inventory and post Dr Inventory / Cr payable-or-cash
                    // BEFORE the product quantity changes.
                    $ledger->receiveStock($receipt, $product);

                    if ($onCredit && $totalCost > 0) {
                        SupplierDebt::create([
                            'business_id' => $businessId,
                            'supplier_id' => $purchaseOrder->supplier_id,
                            'stock_receipt_id' => $receipt->id,
                            'purchase_order_id' => $purchaseOrder->id,
                            'supplier_name' => $purchaseOrder->supplier->name,
                            'reference_number' => $request->invoice_number ?: $receipt->receipt_number,
                            'total_amount' => $totalCost,
                            'amount_paid' => 0,
                            'balance' => $totalCost,
                            'due_date' => $request->due_date ?: now()->addDays(30)->toDateString(),
                            'status' => 'pending',
                            'description' => "PO {$purchaseOrder->po_number}: {$qty} x {$product->name}",
                        ]);
                    }

                    $product->stock_quantity += $qty;
                    $product->cost_price = $unitCost;
                    $product->save();

                    $item->quantity_received += $qty;
                    $item->save();
                }

                $purchaseOrder->refreshStatus();
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to record receipt: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', "Goods received against {$purchaseOrder->po_number}.");
    }
}
