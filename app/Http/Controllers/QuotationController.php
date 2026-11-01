<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\CustomerDebt;
use App\Models\AccountingSetting;
use App\Services\Accounting\Ledger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuotationMail;

class QuotationController extends Controller
{
    private function businessId(): string
    {
        return auth()->user()->business->id;
    }

    private function scopeToBusiness(Quotation $quote): void
    {
        if ((string) $quote->business_id !== $this->businessId()) {
            abort(403, 'Unauthorized access to quotation.');
        }
    }

    public function index(Request $request)
    {
        $businessId = $this->businessId();

        $query = Quotation::where('business_id', $businessId)
            ->with('customer')
            ->withCount('items')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $quotes = $query->paginate(20);
        $customers = Customer::where('business_id', $businessId)->orderBy('name')->get(['id', 'name']);

        $stats = [
            'open'          => Quotation::where('business_id', $businessId)->whereIn('status', ['sent', 'accepted'])->count(),
            'draft'         => Quotation::where('business_id', $businessId)->where('status', 'draft')->count(),
            'open_value'    => Quotation::where('business_id', $businessId)->whereIn('status', ['sent', 'accepted'])->sum('total_amount'),
            'converted'     => Quotation::where('business_id', $businessId)->where('status', 'converted')->count(),
        ];

        return view('quotations.index', compact('quotes', 'customers', 'stats'));
    }

    public function create()
    {
        $business = auth()->user()->business;
        $customers = Customer::where('business_id', $business->id)->orderBy('name')->get(['id', 'name', 'phone', 'email']);
        $products = Product::where('business_id', $business->id)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'measurement_unit', 'sku', 'stock_quantity']);

        return view('quotations.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $businessId = $this->businessId();

        $validated = $request->validate($this->quoteRules($businessId));
        $this->requireCustomer($validated);

        $customer = !empty($validated['customer_id'])
            ? Customer::where('business_id', $businessId)->findOrFail($validated['customer_id'])
            : null;

        $quote = DB::transaction(function () use ($validated, $customer, $businessId, $request) {
            $quote = Quotation::create([
                'business_id'    => $businessId,
                'customer_id'    => $customer?->id,
                'customer_name'  => $customer?->name ?? ($validated['customer_name'] ?? null),
                'customer_email' => $customer?->email ?? ($validated['customer_email'] ?? null),
                'customer_phone' => $customer?->phone ?? ($validated['customer_phone'] ?? null),
                'status'         => $request->boolean('send') ? 'sent' : 'draft',
                'quote_date'     => $validated['quote_date'],
                'valid_until'    => $validated['valid_until'] ?? null,
                'notes'          => $validated['notes'] ?? null,
                'created_by'     => auth()->id(),
                'total_amount'   => 0,
            ]);

            $this->syncItems($quote, $validated['items'], $businessId);

            return $quote;
        });

        if ($request->boolean('send')) {
            $emailed = $this->emailCustomer($quote);
            if ($emailed) {
                return redirect()->route('quotations.show', $quote)
                    ->with('success', "Quotation {$quote->quote_number} created and emailed to {$quote->contact_email}.");
            }
        }

        return redirect()->route('quotations.show', $quote)
            ->with('success', "Quotation {$quote->quote_number} created" . ($quote->status === 'sent' ? ' and marked as sent.' : '.'));
    }

    public function show(Quotation $quotation)
    {
        $this->scopeToBusiness($quotation);
        $quotation->load(['customer', 'items.product', 'order', 'creator']);

        return view('quotations.show', ['quote' => $quotation]);
    }

    public function edit(Quotation $quotation)
    {
        $this->scopeToBusiness($quotation);
        if (!$quotation->editable) {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'Only draft quotations can be edited.');
        }

        $business = auth()->user()->business;
        $customers = Customer::where('business_id', $business->id)->orderBy('name')->get(['id', 'name', 'phone', 'email']);
        $products = Product::where('business_id', $business->id)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'measurement_unit', 'sku', 'stock_quantity']);
        $quotation->load('items');

        return view('quotations.edit', [
            'quote'     => $quotation,
            'customers' => $customers,
            'products'  => $products,
        ]);
    }

    public function update(Request $request, Quotation $quotation)
    {
        $this->scopeToBusiness($quotation);
        if (!$quotation->editable) {
            return back()->with('error', 'Only draft quotations can be edited.');
        }

        $businessId = $this->businessId();
        $validated = $request->validate($this->quoteRules($businessId));
        $this->requireCustomer($validated);

        DB::transaction(function () use ($validated, $quotation, $businessId) {
            $customer = !empty($validated['customer_id'])
                ? Customer::where('business_id', $businessId)->findOrFail($validated['customer_id'])
                : null;

            $quotation->items()->delete();
            $quotation->update([
                'customer_id'    => $customer?->id,
                'customer_name'  => $customer?->name ?? ($validated['customer_name'] ?? null),
                'customer_email' => $customer?->email ?? ($validated['customer_email'] ?? null),
                'customer_phone' => $customer?->phone ?? ($validated['customer_phone'] ?? null),
                'quote_date'     => $validated['quote_date'],
                'valid_until'    => $validated['valid_until'] ?? null,
                'notes'          => $validated['notes'] ?? null,
            ]);

            $this->syncItems($quotation, $validated['items'], $businessId);
        });

        return redirect()->route('quotations.show', $quotation)
            ->with('success', "Quotation {$quotation->quote_number} updated.");
    }

    public function destroy(Quotation $quotation)
    {
        $this->scopeToBusiness($quotation);
        if ($quotation->order_id) {
            return back()->with('error', 'Cannot delete a quotation that was converted to a sale.');
        }

        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }

    public function send(Quotation $quotation)
    {
        $this->scopeToBusiness($quotation);
        if ($quotation->status !== 'draft') {
            return back()->with('error', 'Only draft quotations can be sent.');
        }

        $emailed = $this->emailCustomer($quotation);

        $quotation->status = 'sent';
        $quotation->save();

        if ($emailed) {
            return back()->with('success', "Quotation {$quotation->quote_number} emailed to {$quotation->contact_email}.");
        }

        $reason = $quotation->contact_email
            ? 'the email could not be delivered — check the mail settings and try "Resend Email".'
            : 'no customer email is on record — share the quote manually.';
        return back()->with('warning', "Quotation {$quotation->quote_number} marked as sent, but {$reason}");
    }

    /**
     * Re-email a sent quote to the customer.
     */
    public function resendEmail(Quotation $quotation)
    {
        $this->scopeToBusiness($quotation);
        if ($quotation->status === 'draft') {
            return back()->with('error', 'Send the quotation first.');
        }
        if (!$quotation->contact_email) {
            return back()->with('error', 'This quotation has no customer email on record.');
        }

        if ($this->emailCustomer($quotation)) {
            return back()->with('success', "Quotation {$quotation->quote_number} re-sent to {$quotation->contact_email}.");
        }

        return back()->with('error', 'The email could not be delivered. Check the mail settings and try again.');
    }

    private function emailCustomer(Quotation $quote): bool
    {
        $email = $quote->contact_email;
        if (!$email) {
            return false;
        }

        try {
            Mail::to($email)->send(new QuotationMail($quote));
            return true;
        } catch (\Throwable $e) {
            Log::error('Quotation email failed', [
                'quote_id' => $quote->id,
                'customer_email' => $email,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function markAccepted(Quotation $quotation)
    {
        $this->scopeToBusiness($quotation);
        if ($quotation->status !== 'sent') {
            return back()->with('error', 'Only sent quotations can be marked accepted.');
        }

        $quotation->update(['status' => 'accepted']);
        return back()->with('success', "Quotation {$quotation->quote_number} marked as accepted.");
    }

    public function markDeclined(Quotation $quotation)
    {
        $this->scopeToBusiness($quotation);
        if ($quotation->status !== 'sent') {
            return back()->with('error', 'Only sent quotations can be marked declined.');
        }

        $quotation->update(['status' => 'declined']);
        return back()->with('success', "Quotation {$quotation->quote_number} marked as declined.");
    }

    public function cancel(Quotation $quotation)
    {
        $this->scopeToBusiness($quotation);
        if (!$quotation->cancellable) {
            return back()->with('error', 'This quotation cannot be cancelled.');
        }

        $quotation->update(['status' => 'cancelled']);
        return back()->with('success', "Quotation {$quotation->quote_number} cancelled.");
    }

    /**
     * Convert the quote into a real sale: order + stock issue + ledger postings,
     * plus a customer debt when the sale isn't fully paid — all in one transaction.
     */
    public function convert(Request $request, Quotation $quotation, Ledger $ledger)
    {
        $this->scopeToBusiness($quotation);
        if (!$quotation->convertible) {
            // Already converted (double-submit or back-button resubmit) —
            // just send them to the sale instead of showing an error.
            if ($quotation->order_id) {
                $order = $quotation->order;
                $route = $order->payment_status === 'paid' ? 'sales.print-receipt' : 'sales.generate-invoice';
                return redirect()->route($route, $order)
                    ->with('success', "This quotation was already converted to order {$order->order_number}.");
            }
            return back()->with('error', 'This quotation cannot be converted.');
        }

        $business = auth()->user()->business;
        $businessId = $this->businessId();
        $quotation->load('items.product');

        $validated = $request->validate([
            'payment_method' => 'required|string|in:cash,mpesa,bank_transfer,card,cheque',
            'payment_status' => 'required|string|in:paid,unpaid,partial',
            'amount_paid'    => 'nullable|numeric|min:0',
            'due_date'       => 'nullable|date',
        ]);

        // Stock availability check before anything is written
        foreach ($quotation->items as $item) {
            if ((float) $item->product->stock_quantity < (float) $item->quantity) {
                return back()->with('error',
                    "Insufficient stock for {$item->product_name}: needs {$item->quantity}, only {$item->product->stock_quantity} on hand.");
            }
        }

        try {
            $order = DB::transaction(function () use ($quotation, $validated, $business, $businessId, $ledger) {
                // Resolve the customer: linked record, or create one from the snapshot fields.
                $customer = $quotation->customer;
                if (!$customer) {
                    $customer = Customer::create([
                        'business_id' => $businessId,
                        'name'        => $quotation->customer_name,
                        'email'       => $quotation->customer_email,
                        'phone'       => $quotation->customer_phone,
                    ]);
                }

                // Server-side tax is authoritative (mirrors SalesController::createOrder).
                $products = Product::where('business_id', $businessId)
                    ->whereIn('id', $quotation->items->pluck('product_id'))
                    ->get()
                    ->keyBy('id');

                $taxLines = $quotation->items->map(fn ($item) => [
                    'product'    => $products->get($item->product_id),
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->unit_price,
                ]);
                $taxResult = (new \App\Services\TaxCalculator())->calculate($taxLines, $business->getTaxSettingsOrCreate());
                $lineTax = collect($taxResult['lines'])->keyBy('product_id');

                $total      = (float) $taxResult['total'];
                $amountPaid = match ($validated['payment_status']) {
                    'paid'   => $total,
                    'unpaid' => 0.0,
                    default  => min((float) ($validated['amount_paid'] ?? 0), $total),
                };
                $balanceDue    = round($total - $amountPaid, 2);
                $paymentStatus = $balanceDue <= 0 ? 'paid' : ($amountPaid > 0 ? 'partial' : 'unpaid');

                $order = Order::create([
                    'business_id'     => $businessId,
                    'customer_id'     => $customer->id,
                    'status'          => 'completed',
                    'sold_by'         => auth()->id(),
                    'payment_status'  => $paymentStatus,
                    'payment_method'  => $validated['payment_method'],
                    'notes'           => $quotation->notes,
                    'subtotal'        => $taxResult['subtotal'],
                    'tax'             => $taxResult['tax'],
                    'total_amount'    => $total,
                    'amount_paid'     => $amountPaid,
                    'balance_due'     => $balanceDue,
                    'tax_rate'        => $taxResult['tax_enabled'] ? $taxResult['tax_rate'] : 0,
                    'tax_inclusive'   => $taxResult['tax_enabled'] ? $taxResult['tax_inclusive'] : false,
                    'tax_type'        => $taxResult['tax_enabled'] ? $taxResult['tax_type'] : null,
                    'customer_name'   => $customer->name,
                    'customer_phone'  => $customer->phone,
                    'customer_email'  => $customer->email,
                ]);

                // Unpaid/partial sales get an invoice number for AR tracking (same as POS).
                if ($paymentStatus !== 'paid') {
                    $order->invoice_number = AccountingSetting::for((string) $businessId)->nextInvoiceNumber();
                    $order->invoice_generated_at = now();
                    $order->save();
                }

                if ($balanceDue > 0) {
                    CustomerDebt::create([
                        'business_id' => $businessId,
                        'customer_id' => $customer->id,
                        'order_id'    => $order->id,
                        'total_amount'=> $total,
                        'amount_paid' => $amountPaid,
                        'balance'     => $balanceDue,
                        'due_date'    => $validated['due_date'] ?? now()->addDays(30)->toDateString(),
                        'status'      => $paymentStatus === 'unpaid' ? 'pending' : $paymentStatus,
                        'notes'       => "From quotation {$quotation->quote_number}",
                    ]);
                }

                if ($amountPaid > 0) {
                    $payment = Payment::create([
                        'business_id'    => $businessId,
                        'order_id'       => $order->id,
                        'payment_number' => Payment::generatePaymentNumber(),
                        'amount'         => $amountPaid,
                        'payment_method' => $validated['payment_method'],
                        'payment_date'   => now(),
                        'notes'          => "Payment on quotation {$quotation->quote_number}",
                        'received_by'    => auth()->id(),
                    ]);
                }

                foreach ($quotation->items as $item) {
                    $product = $products->get($item->product_id);
                    $qty = (float) $item->quantity;
                    $taxLine = $lineTax->get($item->product_id);

                    // Value stock out at moving-average cost BEFORE stock_quantity changes.
                    [$unitCost, $costAmount] = $ledger->issueStockForSale($product, $qty, $order);

                    $order->items()->create([
                        'product_id'    => $product->id,
                        'quantity'      => $qty,
                        'stock_qty'     => $qty,
                        'unit_cost'     => $unitCost,
                        'cost_amount'   => $costAmount,
                        'price'         => $item->unit_price,
                        'total'         => $item->total,
                        'tax_amount'    => $taxLine['tax'] ?? 0,
                        'tax_treatment' => $taxLine['treatment'] ?? null,
                        'unit'          => $product->measurement_unit,
                    ]);

                    $product->stock_quantity -= $qty;
                    $product->save();
                }

                $ledger->postSale($order);
                if (!empty($payment)) {
                    $ledger->postPayment($payment);
                }

                $quotation->update([
                    'status'   => 'converted',
                    'order_id' => $order->id,
                ]);

                return $order;
            });
        } catch (\Exception $e) {
            Log::error('Quotation conversion failed', [
                'quote_id' => $quotation->id,
                'error'    => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to convert quotation: ' . $e->getMessage());
        }

        // Land on the document, same as POS: receipt for paid sales, invoice for credit ones.
        $route = $order->payment_status === 'paid' ? 'sales.print-receipt' : 'sales.generate-invoice';
        return redirect()->route($route, $order)
            ->with('success', "Quotation {$quotation->quote_number} converted to order {$order->order_number}.");
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function quoteRules(string $businessId): array
    {
        return [
            'customer_id'          => [
                'nullable',
                \Illuminate\Validation\Rule::exists('customers', 'id')
                    ->where('business_id', $businessId),
            ],
            'customer_name'        => 'nullable|string|max:120',
            'customer_email'       => 'nullable|email|max:191',
            'customer_phone'       => 'nullable|string|max:20',
            'quote_date'           => 'required|date',
            'valid_until'          => 'nullable|date',
            'notes'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|numeric|min:0.001',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'send'                 => 'nullable|boolean',
        ];
    }

    private function requireCustomer(array $validated): void
    {
        if (empty($validated['customer_id']) && empty($validated['customer_name'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'customer_name' => 'Pick a customer or enter a name for a new prospect.',
            ]);
        }
    }

    /** Rebuild the quote's lines and totals (tax is display-only until conversion). */
    private function syncItems(Quotation $quote, array $items, string $businessId): void
    {
        $products = Product::where('business_id', $businessId)
            ->whereIn('id', collect($items)->pluck('product_id'))
            ->get()
            ->keyBy('id');

        $taxLines = collect($items)->map(fn ($line) => [
            'product'    => $products->get($line['product_id']),
            'product_id' => $line['product_id'],
            'quantity'   => $line['quantity'],
            'price'      => $line['unit_price'],
        ]);
        $taxResult = (new \App\Services\TaxCalculator())
            ->calculate($taxLines, auth()->user()->business->getTaxSettingsOrCreate());

        foreach ($items as $line) {
            $product = $products->get($line['product_id']);
            abort_unless($product, 422, 'Invalid product selected.');
            $lineTotal = round($line['quantity'] * $line['unit_price'], 2);

            $quote->items()->create([
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'quantity'     => $line['quantity'],
                'unit_price'   => $line['unit_price'],
                'total'        => $lineTotal,
            ]);
        }

        $quote->update([
            'subtotal'     => $taxResult['subtotal'],
            'tax'          => $taxResult['tax'],
            'total_amount' => $taxResult['total'],
        ]);
    }
}
