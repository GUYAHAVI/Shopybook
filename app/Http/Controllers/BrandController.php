<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Business;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $business = Business::findOrFail($request->business_id);
        
        $brands = Brand::where('business_id', $business->id)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('brands.index', compact('brands', 'business'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $business = Business::findOrFail($request->business_id);

        return view('brands.create', compact('business'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $business = Business::findOrFail($request->business_id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|string',
            'website' => 'nullable|url',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'social_links' => 'nullable|array',
            'social_links.facebook' => 'nullable|url',
            'social_links.instagram' => 'nullable|url',
            'social_links.twitter' => 'nullable|url',
            'social_links.linkedin' => 'nullable|url',
        ]);

        $validated['business_id'] = $business->id;
        $validated['slug'] = Str::slug($validated['name']);

        // Ensure unique slug within business
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Brand::where('business_id', $business->id)->where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Clean up social links
        if (isset($validated['social_links'])) {
            $validated['social_links'] = array_filter($validated['social_links']);
            if (empty($validated['social_links'])) {
                $validated['social_links'] = null;
            }
        }

        $brand = Brand::create($validated);

        return redirect()->route('brands.index', ['business_id' => $business->id])
            ->with('success', 'Brand created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $business = Business::findOrFail($request->business_id);
        $brand = Brand::where('business_id', $business->id)->findOrFail($id);

        return view('brands.show', compact('brand', 'business'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $business = Business::findOrFail($request->business_id);
        $brand = Brand::where('business_id', $business->id)->findOrFail($id);

        return view('brands.edit', compact('brand', 'business'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $business = Business::findOrFail($request->business_id);
        $brand = Brand::where('business_id', $business->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|string',
            'website' => 'nullable|url',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'social_links' => 'nullable|array',
            'social_links.facebook' => 'nullable|url',
            'social_links.instagram' => 'nullable|url',
            'social_links.twitter' => 'nullable|url',
            'social_links.linkedin' => 'nullable|url',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Ensure unique slug within business (excluding current brand)
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Brand::where('business_id', $business->id)
            ->where('slug', $validated['slug'])
            ->where('id', '!=', $brand->id)
            ->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Clean up social links
        if (isset($validated['social_links'])) {
            $validated['social_links'] = array_filter($validated['social_links']);
            if (empty($validated['social_links'])) {
                $validated['social_links'] = null;
            }
        }

        $brand->update($validated);

        return redirect()->route('brands.index', ['business_id' => $business->id])
            ->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $business = Business::findOrFail($request->business_id);
        $brand = Brand::where('business_id', $business->id)->findOrFail($id);

        // Check if brand has products
        if ($brand->products()->count() > 0) {
            return redirect()->route('brands.index', ['business_id' => $business->id])
                ->with('error', 'Cannot delete brand with associated products.');
        }

        $brand->delete();

        return redirect()->route('brands.index', ['business_id' => $business->id])
            ->with('success', 'Brand deleted successfully.');
    }
}
