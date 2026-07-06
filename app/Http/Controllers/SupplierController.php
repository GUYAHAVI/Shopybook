<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentBusiness;

use App\Models\Supplier;
use App\Models\StockReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    use ResolvesCurrentBusiness;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
        }

        $suppliers = Supplier::where('business_id', $business->id)
            ->orderBy('name')
            ->paginate(15);

        // Calculate statistics
        $stats = [
            'month_orders' => StockReceipt::where('business_id', $business->id)
                ->whereMonth('receipt_date', now()->month)
                ->whereYear('receipt_date', now()->year)
                ->count(),
            'total_spent' => StockReceipt::where('business_id', $business->id)
                ->sum('total_cost') ?? 0,
        ];

        return view('suppliers.index', compact('suppliers', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
        }

        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'company_registration' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:255'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'name.required' => 'Supplier name is required.',
            'name.max' => 'Supplier name cannot exceed 255 characters.',
            'email.email' => 'Please enter a valid email address.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'credit_limit.numeric' => 'Credit limit must be a number.',
            'credit_limit.min' => 'Credit limit cannot be negative.',
            'status.required' => 'Please select a status for the supplier.',
            'status.in' => 'Status must be either active or inactive.',
        ]);

        try {
            Supplier::create([
                'business_id' => $business->id,
                ...$validated
            ]);

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier created successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create supplier. Please check all fields and try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $this->authorize('view', $supplier);
        
        // Get purchase history from stock receipts
        $purchaseHistory = StockReceipt::where('business_id', $supplier->business_id)
            ->where('supplier', $supplier->name)
            ->orderBy('receipt_date', 'desc')
            ->paginate(10);

        // Get products from this supplier
        $products = $supplier->products()->get();

        // Calculate statistics
        $stats = [
            'total_orders' => StockReceipt::where('business_id', $supplier->business_id)
                ->where('supplier', $supplier->name)
                ->count(),
            'total_spent' => StockReceipt::where('business_id', $supplier->business_id)
                ->where('supplier', $supplier->name)
                ->sum('total_cost') ?? 0,
            'month_spent' => StockReceipt::where('business_id', $supplier->business_id)
                ->where('supplier', $supplier->name)
                ->whereMonth('receipt_date', now()->month)
                ->whereYear('receipt_date', now()->year)
                ->sum('total_cost') ?? 0,
            'last_order' => StockReceipt::where('business_id', $supplier->business_id)
                ->where('supplier', $supplier->name)
                ->latest('receipt_date')
                ->value('receipt_date')
                ? StockReceipt::where('business_id', $supplier->business_id)
                    ->where('supplier', $supplier->name)
                    ->latest('receipt_date')
                    ->first()
                    ->receipt_date->diffForHumans()
                : 'Never',
        ];
        
        return view('suppliers.show', compact('supplier', 'purchaseHistory', 'products', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        $this->authorize('update', $supplier);
        
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $this->authorize('update', $supplier);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'company_registration' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:255'],
            'payment_terms' => ['nullable', 'string', 'max:255'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'name.required' => 'Supplier name is required.',
            'name.max' => 'Supplier name cannot exceed 255 characters.',
            'email.email' => 'Please enter a valid email address.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'credit_limit.numeric' => 'Credit limit must be a number.',
            'credit_limit.min' => 'Credit limit cannot be negative.',
            'status.required' => 'Please select a status for the supplier.',
            'status.in' => 'Status must be either active or inactive.',
        ]);

        try {
            $supplier->update($validated);

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier updated successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update supplier. Please check all fields and try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $this->authorize('delete', $supplier);
        
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully!');
    }
}
