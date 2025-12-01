<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductConversion;
use Illuminate\Support\Facades\Auth;

class DynamicConversionService
{
    /**
     * Get available conversion options for a product
     */
    public function getConversionOptions(Product $product): array
    {
        $business = Auth::user()->business;
        
        // Check if business is eligible for dynamic conversions
        if (!$business->isEligibleForDynamicConversions()) {
            return [];
        }
        
        $conversions = ProductConversion::where('business_id', $business->id)
            ->where('product_id', $product->id)
            ->get();

        $options = [];

        foreach ($conversions as $conversion) {
            $options[] = [
                'id' => $conversion->id,
                'from_unit' => $conversion->purchase_unit,
                'to_unit' => $conversion->sale_unit,
                'conversion_factor' => $conversion->conversion_factor,
                'conversion_type' => $conversion->conversion_type,
                'purchase_cost' => $conversion->purchase_cost,
                'sale_price' => $conversion->sale_price,
                'label' => "{$conversion->purchase_unit} → {$conversion->sale_unit}",
                'description' => $this->getConversionDescription($conversion)
            ];
        }

        return $options;
    }

    /**
     * Calculate conversion for a given quantity and target unit
     */
    public function calculateConversion(Product $product, float $quantity, string $fromUnit, string $toUnit): array
    {
        $business = Auth::user()->business;
        
        // Check if business is eligible for dynamic conversions
        if (!$business->isEligibleForDynamicConversions()) {
            return [
                'success' => false,
                'message' => 'Dynamic Conversion System is only available for Havi\'s Greenhouse Materials.'
            ];
        }
        
        // Find the conversion rule
        $conversion = ProductConversion::where('business_id', $business->id)
            ->where('product_id', $product->id)
            ->where('purchase_unit', $fromUnit)
            ->where('sale_unit', $toUnit)
            ->first();

        if (!$conversion) {
            return [
                'success' => false,
                'message' => "No conversion rule found for {$fromUnit} to {$toUnit}"
            ];
        }

        // Calculate converted quantity
        $convertedQuantity = $this->performConversion($quantity, $conversion->conversion_factor, $conversion->conversion_type);
        
        // Calculate costs and profits
        $purchaseTotal = $quantity * $conversion->purchase_cost;
        $saleTotal = $convertedQuantity * $conversion->sale_price;
        $profit = $saleTotal - $purchaseTotal;
        $profitMargin = $purchaseTotal > 0 ? ($profit / $purchaseTotal) * 100 : 0;

        return [
            'success' => true,
            'from_quantity' => $quantity,
            'from_unit' => $fromUnit,
            'to_quantity' => $convertedQuantity,
            'to_unit' => $toUnit,
            'conversion_factor' => $conversion->conversion_factor,
            'purchase_cost_per_unit' => $conversion->purchase_cost,
            'sale_price_per_unit' => $conversion->sale_price,
            'purchase_total' => $purchaseTotal,
            'sale_total' => $saleTotal,
            'profit' => $profit,
            'profit_margin' => $profitMargin,
            'formula' => $this->getConversionFormula($quantity, $conversion->conversion_factor, $conversion->conversion_type, $fromUnit, $toUnit)
        ];
    }

    /**
     * Get all possible conversion combinations for a product
     */
    public function getAllConversionCombinations(Product $product): array
    {
        $business = Auth::user()->business;
        $conversions = ProductConversion::where('business_id', $business->id)
            ->where('product_id', $product->id)
            ->get();

        $combinations = [];

        foreach ($conversions as $conversion) {
            $combinations[] = [
                'from_unit' => $conversion->purchase_unit,
                'to_unit' => $conversion->sale_unit,
                'conversion_factor' => $conversion->conversion_factor,
                'conversion_type' => $conversion->conversion_type,
                'label' => "{$conversion->purchase_unit} → {$conversion->sale_unit}",
                'description' => $this->getConversionDescription($conversion)
            ];
        }

        return $combinations;
    }

    /**
     * Perform the actual conversion calculation
     */
    private function performConversion(float $quantity, float $factor, string $type): float
    {
        switch ($type) {
            case 'weight_to_area':
                return $quantity / $factor; // kg to sqm: divide by microns
            case 'area_to_weight':
                return $quantity * $factor; // sqm to kg: multiply by microns
            case 'custom':
                return $quantity * $factor; // custom conversion
            default:
                return $quantity;
        }
    }

    /**
     * Get conversion formula description
     */
    private function getConversionFormula(float $quantity, float $factor, string $type, string $fromUnit, string $toUnit): string
    {
        switch ($type) {
            case 'weight_to_area':
                return "{$quantity} {$fromUnit} ÷ {$factor} = " . number_format($quantity / $factor, 2) . " {$toUnit}";
            case 'area_to_weight':
                return "{$quantity} {$fromUnit} × {$factor} = " . number_format($quantity * $factor, 2) . " {$toUnit}";
            case 'custom':
                return "{$quantity} {$fromUnit} × {$factor} = " . number_format($quantity * $factor, 2) . " {$toUnit}";
            default:
                return "{$quantity} {$fromUnit} = " . number_format($quantity, 2) . " {$toUnit}";
        }
    }

    /**
     * Get conversion description
     */
    private function getConversionDescription(ProductConversion $conversion): string
    {
        switch ($conversion->conversion_type) {
            case 'weight_to_area':
                return "Convert {$conversion->purchase_unit} to {$conversion->sale_unit} using {$conversion->conversion_factor} micron thickness";
            case 'area_to_weight':
                return "Convert {$conversion->purchase_unit} to {$conversion->sale_unit} using {$conversion->conversion_factor} micron thickness";
            case 'custom':
                return "Custom conversion from {$conversion->purchase_unit} to {$conversion->sale_unit}";
            default:
                return "Conversion from {$conversion->purchase_unit} to {$conversion->sale_unit}";
        }
    }

    /**
     * Get suggested sale price for a given purchase quantity and target unit
     */
    public function getSuggestedSalePrice(Product $product, float $purchaseQuantity, string $fromUnit, string $toUnit, float $desiredProfitMargin = 20): array
    {
        $conversion = $this->calculateConversion($product, $purchaseQuantity, $fromUnit, $toUnit);
        
        if (!$conversion['success']) {
            return $conversion;
        }

        // Calculate suggested sale price to achieve desired profit margin
        $purchaseTotal = $conversion['purchase_total'];
        $desiredSaleTotal = $purchaseTotal * (1 + ($desiredProfitMargin / 100));
        $suggestedSalePrice = $desiredSaleTotal / $conversion['to_quantity'];

        return array_merge($conversion, [
            'suggested_sale_price' => $suggestedSalePrice,
            'desired_profit_margin' => $desiredProfitMargin,
            'desired_sale_total' => $desiredSaleTotal
        ]);
    }

    /**
     * Get quick conversion examples for a product
     */
    public function getQuickExamples(Product $product): array
    {
        $business = Auth::user()->business;
        
        // Check if business is eligible for dynamic conversions
        if (!$business->isEligibleForDynamicConversions()) {
            return [];
        }
        
        $conversions = ProductConversion::where('business_id', $business->id)
            ->where('product_id', $product->id)
            ->get();

        $examples = [];

        foreach ($conversions as $conversion) {
            // Generate example with 100 units
            $exampleQuantity = 100;
            $convertedQuantity = $this->performConversion($exampleQuantity, $conversion->conversion_factor, $conversion->conversion_type);
            
            $examples[] = [
                'from_unit' => $conversion->purchase_unit,
                'to_unit' => $conversion->sale_unit,
                'example' => "{$exampleQuantity} {$conversion->purchase_unit} = " . number_format($convertedQuantity, 2) . " {$conversion->sale_unit}",
                'factor' => $conversion->conversion_factor,
                'type' => $conversion->conversion_type
            ];
        }

        return $examples;
    }
}
