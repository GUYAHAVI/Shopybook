<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BusinessController extends Controller
{
    /**
     * Display a listing of businesses
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'name');
        $order = $request->get('order', 'asc');
        $type = $request->get('type', 'all');

        $query = Business::query();

        if ($type !== 'all') {
            $query->where('business_type', $type);
        }

        $groupedBusinesses = $query->orderBy($sort, $order)
            ->get()
            ->groupBy('business_category');

        return view('businesses', compact('groupedBusinesses', 'sort', 'order', 'type'));
    }

    /**
     * Show the business type selection page
     */
  public function chooseType()
{
    // Use same access method as dashboard
    if (auth()->user()->business()->exists()) {
        return redirect()->route('dashboard');
    }

    return view('business.choose-type');
}

    /**
     * Show the form for creating a new business
     */
    public function create(Request $request)
    {
        // Check if user already has a business
        if (Auth::user()->business) {
            return redirect()->route('dashboard');
        }

        $type = $request->get('type', 'product');
        
        // Map business types to appropriate categories
        $businessTypeOptions = [
            'product' => [
                'retail' => 'Retail Shop',
                'online' => 'Online Store',
                'fashion' => 'Fashion & Clothing',
                'electronics' => 'Electronics',
                'grocery' => 'Grocery Store',
                'beauty' => 'Beauty & Cosmetics',
                'wholesale' => 'Wholesale Business',
                'other_product' => 'Other Product Business',
            ],
            'service' => [
                'consulting' => 'Consulting Services',
                'beauty_service' => 'Beauty & Wellness Services',
                'repair' => 'Repair Services',
                'cleaning' => 'Cleaning Services',
                'education' => 'Education & Training',
                'healthcare' => 'Healthcare Services',
                'professional' => 'Professional Services',
                'other_service' => 'Other Service Business',
            ],
            'hybrid' => [
                'restaurant' => 'Restaurant',
                'salon' => 'Salon & Spa',
                'auto_service' => 'Auto Service Center',
                'retail_service' => 'Retail with Services',
                'tech_service' => 'Technology Sales & Support',
                'other_hybrid' => 'Other Hybrid Business',
            ]
        ];

        return view('business.create', [
            'businessTypes' => $businessTypeOptions[$type] ?? $businessTypeOptions['product'],
            'selectedType' => $type,
            'typeTitle' => ucfirst($type) . ' Business'
        ]);
    }

    /**
     * Store a newly created business
     */
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

        try {
            // Determine business category based on business type
            $businessCategory = $this->getBusinessCategory($validated['business_type']);
            
            // Create the business
            $business = Business::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'],
                'business_type' => $validated['business_type'],
                'business_category' => $businessCategory,
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'],
                'country' => 'Kenya',
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('business/logos', 'public');
                $business->update(['logo_path' => $path]);
            }

            Log::info('Business created successfully', [
                'business_id' => $business->id,
                'user_id' => Auth::id(),
                'business_name' => $business->name
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Business created successfully! Welcome to Shopybook.');

        } catch (\Exception $e) {
            Log::error('Failed to create business', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to create business. Please try again.');
        }
    }

    /**
     * Show the form for editing the business
     */
    public function edit()
    {
        $business = Auth::user()->business;
        
        if (!$business) {
            return redirect()->route('business.create');
        }

        return view('business.edit', compact('business'));
    }

    /**
     * Update the specified business
     */
    public function update(Request $request)
    {
        $business = Auth::user()->business;
        
        if (!$business) {
            return redirect()->route('business.create');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:businesses,name,' . $business->id,
            'description' => 'nullable|string',
            'email' => 'nullable|email|unique:businesses,email,' . $business->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'required|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        try {
            $business->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'],
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'],
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('business/logos', 'public');
                $business->update(['logo_path' => $path]);
            }

            return redirect()->route('business.edit')
                ->with('success', 'Business updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update business', [
                'business_id' => $business->id,
                'error' => $e->getMessage()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update business. Please try again.');
        }
    }

    /**
     * Remove the specified business
     */
    public function destroy(Business $business, Request $request)
    {
        // Verify the business belongs to the authenticated user
        if ($business->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $businessName = $business->name;
            $business->delete();

            Log::info('Business deleted successfully', [
                'business_id' => $business->id,
                'user_id' => Auth::id(),
                'business_name' => $businessName
            ]);

            return redirect()->route('business.create')
                ->with('success', 'Business "' . $businessName . '" has been deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete business', [
                'business_id' => $business->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to delete business. Please try again.');
        }
    }

    /**
     * Display the specified business
     */
    public function show($slug)
    {
        Log::info("=== Business Show Debug ===");
        Log::info("Requested slug: " . $slug);
        Log::info("Request URL: " . request()->fullUrl());
        
        // Let's check all businesses and their slugs
        $allBusinesses = Business::select('id', 'slug', 'name')->get();
        Log::info("All businesses in database:", $allBusinesses->toArray());
        
        // Check if the slug exists in database
        $slugExists = Business::where('slug', $slug)->exists();
        Log::info("Does slug '$slug' exist in database: " . ($slugExists ? 'YES' : 'NO'));
        
        // Try different approaches to find the business
        $business1 = Business::where('slug', $slug)->first();
        $business2 = Business::where('slug', 'LIKE', $slug)->first();
        $business3 = Business::find($slug); // In case slug is actually an ID
        
        Log::info("Query results:");
        Log::info("Method 1 (exact match): " . ($business1 ? $business1->name . " (ID: " . $business1->id . ")" : 'NULL'));
        Log::info("Method 2 (LIKE match): " . ($business2 ? $business2->name . " (ID: " . $business2->id . ")" : 'NULL'));
        Log::info("Method 3 (by ID): " . ($business3 ? $business3->name . " (ID: " . $business3->id . ")" : 'NULL'));
        
        $business = $business1; // Use the first method as primary
        
        if (!$business) {
            Log::error("Business not found with slug: " . $slug);
            return redirect()->route('businesses')->with('error', 'Business not found.');
        }
        
        Log::info("Final selected business: " . $business->name . " (ID: " . $business->id . ")");
        
        // Get services and products for this business
        $services = $business->services()->get() ?? collect();
        $products = $business->products()->get() ?? collect();
        
        Log::info("Services count: " . $services->count() . ", Products count: " . $products->count());
        Log::info("=== End Business Show Debug ===");
        
        return view('business.show', compact('business', 'services', 'products'));
    }

    /**
     * Determine business category based on business type
     */
    private function getBusinessCategory($businessType)
    {
        $productTypes = ['retail', 'online', 'fashion', 'electronics', 'grocery', 'beauty', 'wholesale', 'other_product'];
        $serviceTypes = ['consulting', 'beauty_service', 'repair', 'cleaning', 'education', 'healthcare', 'professional', 'other_service'];
        $hybridTypes = ['restaurant', 'salon', 'auto_service', 'retail_service', 'tech_service', 'other_hybrid'];

        if (in_array($businessType, $productTypes)) {
            return 'product';
        } elseif (in_array($businessType, $serviceTypes)) {
            return 'service';
        } elseif (in_array($businessType, $hybridTypes)) {
            return 'hybrid';
        }

        return 'other';
    }
}
