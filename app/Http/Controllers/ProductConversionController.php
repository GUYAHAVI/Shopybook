<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductConversion;
use App\Services\DynamicConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductConversionController extends Controller
{
    public function index()
    {
        $business = Auth::user()->business;
        $conversions = ProductConversion::where('business_id', $business->id)
            ->with('product')
            ->latest()
            ->paginate(15);
            
        return view('business.products.conversions.index', compact('conversions', 'business'));
    }

    public function create()
    {
        $business = Auth::user()->business;
        $products = $business->products()->where('is_active', true)->get();
        $conversionTypes = ProductConversion::getConversionTypes();
        $units = ProductConversion::getCommonUnits();
        $microns = ProductConversion::getCommonMicrons();
        
        return view('business.products.conversions.create', compact('products', 'conversionTypes', 'units', 'microns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'conversion_type' => 'required|in:weight_to_area,area_to_weight,custom',
            'purchase_unit' => 'required|string',
            'sale_unit' => 'required|string',
            'conversion_factor' => 'required|numeric|min:0.0001',
            'purchase_quantity' => 'required|numeric|min:0.01',
            'purchase_cost' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $business = Auth::user()->business;
        
        // Verify the product belongs to this business
        $product = $business->products()->findOrFail($request->product_id);
        
        $conversion = new ProductConversion($request->all());
        $conversion->business_id = $business->id;
        $conversion->calculateConvertedQuantity();
        $conversion->save();

        return redirect()->route('product-conversions.index')
            ->with('success', 'Product conversion created successfully!');
    }

    public function show(ProductConversion $conversion)
    {
        $this->authorize('view', $conversion);
        
        return view('business.products.conversions.show', compact('conversion'));
    }

    public function edit(ProductConversion $conversion)
    {
        $this->authorize('update', $conversion);
        
        $business = Auth::user()->business;
        $products = $business->products()->where('is_active', true)->get();
        $conversionTypes = ProductConversion::getConversionTypes();
        $units = ProductConversion::getCommonUnits();
        $microns = ProductConversion::getCommonMicrons();
        
        return view('business.products.conversions.edit', compact('conversion', 'products', 'conversionTypes', 'units', 'microns'));
    }

    public function update(Request $request, ProductConversion $conversion)
    {
        $this->authorize('update', $conversion);
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'conversion_type' => 'required|in:weight_to_area,area_to_weight,custom',
            'purchase_unit' => 'required|string',
            'sale_unit' => 'required|string',
            'conversion_factor' => 'required|numeric|min:0.0001',
            'purchase_quantity' => 'required|numeric|min:0.01',
            'purchase_cost' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $conversion->fill($request->all());
        $conversion->calculateConvertedQuantity();
        $conversion->save();

        return redirect()->route('product-conversions.index')
            ->with('success', 'Product conversion updated successfully!');
    }

    public function destroy(ProductConversion $conversion)
    {
        $this->authorize('delete', $conversion);
        
        $conversion->delete();

        return redirect()->route('product-conversions.index')
            ->with('success', 'Product conversion deleted successfully!');
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'conversion_type' => 'required|in:weight_to_area,area_to_weight,custom',
            'purchase_quantity' => 'required|numeric|min:0.01',
            'conversion_factor' => 'required|numeric|min:0.0001',
        ]);

        $conversion = new ProductConversion($request->all());
        $convertedQuantity = $conversion->calculateConvertedQuantity();

        return response()->json([
            'converted_quantity' => round($convertedQuantity, 2),
            'formatted_conversion' => $conversion->formatted_conversion
        ]);
    }

    public function productConversions(Product $product)
    {
        $this->authorize('view', $product);
        
        $conversions = $product->conversions()->latest()->get();
        
        return view('business.products.conversions.product', compact('product', 'conversions'));
    }

    /**
     * Dynamic conversion calculator for flexible selling
     */
    public function dynamicCalculator(Product $product)
    {
        $this->authorize('view', $product);
        
        $business = Auth::user()->business;
        
        // Check if business is eligible for dynamic conversions
        if (!$business->isEligibleForDynamicConversions()) {
            return redirect()->route('product-conversions.index')
                ->with('error', 'Dynamic Conversion System is only available for Havi\'s Greenhouse Materials.');
        }
        
        $dynamicService = new DynamicConversionService();
        $conversionOptions = $dynamicService->getConversionOptions($product);
        $quickExamples = $dynamicService->getQuickExamples($product);
        
        return view('business.products.conversions.dynamic-calculator', compact('product', 'conversionOptions', 'quickExamples'));
    }

    /**
     * AJAX endpoint for dynamic conversion calculation
     */
    public function calculateDynamic(Request $request, Product $product)
    {
        $this->authorize('view', $product);
        
        $business = Auth::user()->business;
        
        // Check if business is eligible for dynamic conversions
        if (!$business->isEligibleForDynamicConversions()) {
            return response()->json([
                'success' => false,
                'message' => 'Dynamic Conversion System is only available for Havi\'s Greenhouse Materials.'
            ], 403);
        }
        
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'from_unit' => 'required|string',
            'to_unit' => 'required|string'
        ]);

        $dynamicService = new DynamicConversionService();
        $result = $dynamicService->calculateConversion(
            $product,
            $request->quantity,
            $request->from_unit,
            $request->to_unit
        );

        return response()->json($result);
    }

    /**
     * Get conversion options for a product (for POS)
     */
    public function getProductConversions(Product $product)
    {
        $this->authorize('view', $product);
        
        $business = Auth::user()->business;
        
        // Check if business is eligible for dynamic conversions
        if (!$business->isEligibleForDynamicConversions()) {
            return response()->json([
                'success' => false,
                'message' => 'Dynamic Conversion System is only available for Havi\'s Greenhouse Materials.'
            ], 403);
        }

        $dynamicService = new DynamicConversionService();
        $conversions = $dynamicService->getConversionOptions($product);

        return response()->json([
            'success' => true,
            'conversions' => $conversions
        ]);
    }

    /**
     * Get conversion options for a product (alternative endpoint for POS)
     */
    public function getConversionOptions(Product $product)
    {
        return $this->getProductConversions($product);
    }

    /**
     * Get suggested sale price for dynamic conversion
     */
    public function getSuggestedPrice(Request $request, Product $product)
    {
        $this->authorize('view', $product);
        
        $business = Auth::user()->business;
        
        // Check if business is eligible for dynamic conversions
        if (!$business->isEligibleForDynamicConversions()) {
            return response()->json([
                'success' => false,
                'message' => 'Dynamic Conversion System is only available for Havi\'s Greenhouse Materials.'
            ], 403);
        }
        
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'from_unit' => 'required|string',
            'to_unit' => 'required|string',
            'profit_margin' => 'nullable|numeric|min:0|max:100'
        ]);

        $dynamicService = new DynamicConversionService();
        $profitMargin = $request->profit_margin ?? 20;
        
        $result = $dynamicService->getSuggestedSalePrice(
            $product,
            $request->quantity,
            $request->from_unit,
            $request->to_unit,
            $profitMargin
        );

        return response()->json($result);
    }
}
