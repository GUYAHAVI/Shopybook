<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Business;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $business = Business::findOrFail($request->business_id);
        
        $categories = Category::where('business_id', $business->id)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->parent_id, function ($query, $parentId) {
                $query->where('parent_id', $parentId);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('categories.index', compact('categories', 'business'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $business = Business::findOrFail($request->business_id);
        $parentCategories = Category::where('business_id', $business->id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('categories.create', compact('business', 'parentCategories'));
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
            'parent_id' => 'nullable|exists:categories,id',
            'image_path' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $validated['business_id'] = $business->id;
        $validated['slug'] = Str::slug($validated['name']);

        // Ensure unique slug within business
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Category::where('business_id', $business->id)->where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $category = Category::create($validated);

        return redirect()->route('categories.index', ['business_id' => $business->id])
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $business = Business::findOrFail($request->business_id);
        $category = Category::where('business_id', $business->id)->findOrFail($id);

        return view('categories.show', compact('category', 'business'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $business = Business::findOrFail($request->business_id);
        $category = Category::where('business_id', $business->id)->findOrFail($id);
        $parentCategories = Category::where('business_id', $business->id)
            ->whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('categories.edit', compact('category', 'business', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $business = Business::findOrFail($request->business_id);
        $category = Category::where('business_id', $business->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($category) {
                    if ($value == $category->id) {
                        $fail('A category cannot be its own parent.');
                    }
                }
            ],
            'image_path' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Ensure unique slug within business (excluding current category)
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Category::where('business_id', $business->id)
            ->where('slug', $validated['slug'])
            ->where('id', '!=', $category->id)
            ->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $category->update($validated);

        return redirect()->route('categories.index', ['business_id' => $business->id])
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $business = Business::findOrFail($request->business_id);
        $category = Category::where('business_id', $business->id)->findOrFail($id);

        // Check if category has children
        if ($category->children()->count() > 0) {
            return redirect()->route('categories.index', ['business_id' => $business->id])
                ->with('error', 'Cannot delete category with subcategories.');
        }

        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index', ['business_id' => $business->id])
                ->with('error', 'Cannot delete category with associated products.');
        }

        $category->delete();

        return redirect()->route('categories.index', ['business_id' => $business->id])
            ->with('success', 'Category deleted successfully.');
    }
}
