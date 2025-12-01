<?php

namespace App\Services;

use App\Models\ProductConversion;

class ProductConversionService
{
    /**
     * Calculate converted quantity based on conversion type and factor
     */
    public function calculateConvertedQuantity(float $purchaseQuantity, string $conversionType, float $conversionFactor): float
    {
        switch ($conversionType) {
            case 'weight_to_area':
                // kg to sqm: divide by microns (conversion_factor)
                return $purchaseQuantity / $conversionFactor;
                
            case 'area_to_weight':
                // sqm to kg: multiply by microns (conversion_factor)
                return $purchaseQuantity * $conversionFactor;
                
            case 'custom':
                // Use custom conversion factor
                return $purchaseQuantity * $conversionFactor;
                
            default:
                return $purchaseQuantity;
        }
    }

    /**
     * Calculate profit margin for a conversion
     */
    public function calculateProfitMargin(float $purchaseQuantity, float $purchaseCost, float $convertedQuantity, float $salePrice): float
    {
        $purchaseTotal = $purchaseQuantity * $purchaseCost;
        $saleTotal = $convertedQuantity * $salePrice;
        
        if ($purchaseTotal == 0) return 0;
        
        return (($saleTotal - $purchaseTotal) / $purchaseTotal) * 100;
    }

    /**
     * Get common micron values for greenhouse materials
     */
    public function getCommonMicrons(): array
    {
        return [
            '0.2' => '0.2 microns (Greenhouse Film)',
            '0.3' => '0.3 microns (Dam Liner)',
            '0.5' => '0.5 microns (Dam Liner)',
            '0.75' => '0.75 microns (Dam Liner)',
            '1.0' => '1.0 microns (Dam Liner)',
            'custom' => 'Custom microns'
        ];
    }

    /**
     * Get conversion examples for common greenhouse materials
     */
    public function getConversionExamples(): array
    {
        return [
            [
                'name' => 'Greenhouse Film (0.2 microns)',
                'example' => '100 kg ÷ 0.2 = 500 sqm',
                'description' => 'Bought by weight, sold by area'
            ],
            [
                'name' => 'Dam Liner (0.5 microns)',
                'example' => '50 kg ÷ 0.5 = 100 sqm',
                'description' => 'Bought by weight, sold by area'
            ],
            [
                'name' => 'Dam Liner (1.0 microns)',
                'example' => '200 kg ÷ 1.0 = 200 sqm',
                'description' => 'Bought by weight, sold by area'
            ]
        ];
    }

    /**
     * Validate conversion parameters
     */
    public function validateConversion(string $conversionType, float $conversionFactor, string $purchaseUnit, string $saleUnit): array
    {
        $errors = [];

        if ($conversionFactor <= 0) {
            $errors[] = 'Conversion factor must be greater than 0';
        }

        if ($conversionType === 'weight_to_area') {
            if ($purchaseUnit !== 'kg') {
                $errors[] = 'Purchase unit should be kg for weight to area conversion';
            }
            if ($saleUnit !== 'sqm') {
                $errors[] = 'Sale unit should be sqm for weight to area conversion';
            }
        }

        if ($conversionType === 'area_to_weight') {
            if ($purchaseUnit !== 'sqm') {
                $errors[] = 'Purchase unit should be sqm for area to weight conversion';
            }
            if ($saleUnit !== 'kg') {
                $errors[] = 'Sale unit should be kg for area to weight conversion';
            }
        }

        return $errors;
    }
}






