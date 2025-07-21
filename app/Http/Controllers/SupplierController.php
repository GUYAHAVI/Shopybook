<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $suppliers = Supplier::where('business_id', $business->id)
            ->orderBy('name')
            ->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'company_registration' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Supplier::create([
            'business_id' => $business->id,
            ...$validated
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $this->authorize('view', $supplier);
        
        return view('suppliers.show', compact('supplier'));
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
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'company_registration' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully!');
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
