<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
// Log class for logging
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class BusinessController extends Controller
{


    public function index(Request $request)
    {
        $sort = $request->get('sort', 'name');
        $order = $request->get('order', 'asc');

        $businessTypes = Business::select('business_type')
            ->distinct()
            ->orderBy('business_type')
            ->pluck('business_type');

        $groupedBusinesses = [];

        foreach ($businessTypes as $type) {
            $groupedBusinesses[$type] = Business::where('business_type', $type)
                ->where('active', true)
                ->orderBy($sort, $order)
                ->get();
        }

        return view('businesses', compact('groupedBusinesses', 'sort', 'order'));
    }
    public function create()
    {
        if (auth()->user()->business) {
            return redirect()->route('dashboard');
        }

        return view('business.create', [
            'businessTypes' => [
                'retail' => 'Retail Shop',
                'restaurant' => 'Restaurant',
                'service' => 'Service Provider',
                'online' => 'Online Store',
                'fashion' => 'Fashion & Clothing',
                'electronics' => 'Electronics',
                'grocery' => 'Grocery Store',
                'beauty' => 'Beauty & Cosmetics',
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:businesses',
            'business_type' => 'required|string',
            'description' => 'nullable|string',
            'email' => 'nullable|email|unique:businesses',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'required|string',
            'terms' => 'required|accepted',
            'logo' => 'nullable|image|max:2048',
        ]);

        // Create the tenant (business)
        $business = Business::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'business_type' => $validated['business_type'],
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'],
            'country' => 'Kenya', // or get from request if you have that field
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('business/logos', 'public');
            $business->update(['logo_path' => $path]);
        }

        // Initialize the tenant (creates database, runs migrations, etc.)
        tenancy()->initialize($business);

        return redirect()->route('dashboard')->with('success', 'Business created successfully!');
    }

    public function edit(Business $business)
    {
        return view('business.edit', [
            'business' => $business,
            'businessTypes' => [
                'retail' => 'Retail Shop',
                'restaurant' => 'Restaurant',
                'service' => 'Service Provider',
                'online' => 'Online Store',
                'fashion' => 'Fashion & Clothing',
                'electronics' => 'Electronics',
                'grocery' => 'Grocery Store',
                'beauty' => 'Beauty & Cosmetics',
            ]
        ]);
    }

    public function update(Request $request, Business $business)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('businesses')->ignore($business->id)],
            'email' => ['nullable', 'email', Rule::unique('businesses')->ignore($business->id)],
            'phone' => 'required|string|max:20',
            'business_type' => 'required|string',
            'description' => 'nullable|string|max:500',
            'logo' => 'nullable|image|max:2048|dimensions:min_width=100,min_height=100',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'business_type' => $validated['business_type'],
            'description' => $validated['description'],
            'address' => $validated['address'],
            'city' => $validated['city'],
        ];

        // Handle logo update
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($business->logo_path) {
                Storage::disk('public')->delete($business->logo_path);
            }

            $updateData['logo_path'] = $request->file('logo')->store('business/logos', 'public');
        }

        $business->update($updateData);

        return back()->with('success', 'Business profile updated successfully!');
    }

    public function destroy(Business $business, Request $request)
    {
        // Check if user can delete this business
        $this->authorize('delete', $business);

        if (!Hash::check($request->input('password'), auth()->user()->getAuthPassword())) {
            return back()->with('error', 'Invalid password. Deletion canceled.');
        }
        try {
            // Delete logo if exists
            if ($business->logo_path) {
                Storage::disk('public')->delete($business->logo_path);
            }

            // Delete cover if exists
            if ($business->cover_path) {
                Storage::disk('public')->delete($business->cover_path);
            }

            // Proper way to delete a tenant
            $business->delete(); // This will trigger all tenant cleanup

            // OR if you need more control:
            // tenancy()->end();
            // $business->purge();

            return redirect()->route('dashboard')
                ->with('success', 'Business and all its data deleted successfully');

        } catch (\Exception $e) {
            \Log::error('Business deletion failed', [
                'error' => $e->getMessage(),
                'business_id' => $business->id
            ]);

            return back()->with('error', 'Failed to delete business: ' . $e->getMessage());
        }
    }
    public function show(Business $business)
    {
        // Ensure the business is active
        if (!$business->active) {
            return redirect()->route('business.index')->with('error', 'This business is not active.');
        }

        return view('business.show', compact('business'));
    }

}