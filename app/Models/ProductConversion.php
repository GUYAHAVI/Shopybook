<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'product_id',
        'conversion_type',
        'purchase_unit',
        'sale_unit',
        'conversion_factor',
        'purchase_quantity',
        'converted_quantity',
        'purchase_cost',
        'sale_price',
        'notes'
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:4',
        'purchase_quantity' => 'decimal:2',
        'converted_quantity' => 'decimal:2',
        'purchase_cost' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Methods
    public function calculateConvertedQuantity()
    {
        switch ($this->conversion_type) {
            case 'weight_to_area':
                // kg to sqm: divide by microns (conversion_factor)
                $this->converted_quantity = $this->purchase_quantity / $this->conversion_factor;
                break;
                
            case 'area_to_weight':
                // sqm to kg: multiply by microns (conversion_factor)
                $this->converted_quantity = $this->purchase_quantity * $this->conversion_factor;
                break;
                
            case 'custom':
                // Use custom conversion factor
                $this->converted_quantity = $this->purchase_quantity * $this->conversion_factor;
                break;
                
            default:
                $this->converted_quantity = $this->purchase_quantity;
        }
        
        return $this->converted_quantity;
    }

    public function calculateProfitMargin()
    {
        if ($this->purchase_cost == 0) return 0;
        
        $purchaseTotal = $this->purchase_quantity * $this->purchase_cost;
        $saleTotal = $this->converted_quantity * $this->sale_price;
        
        return (($saleTotal - $purchaseTotal) / $purchaseTotal) * 100;
    }

    public function getFormattedConversionAttribute()
    {
        return "{$this->purchase_quantity} {$this->purchase_unit} = {$this->converted_quantity} {$this->sale_unit}";
    }

    public function getFormattedProfitMarginAttribute()
    {
        $margin = $this->calculateProfitMargin();
        return number_format($margin, 1) . '%';
    }

    // Static methods for common conversions
    public static function getConversionTypes()
    {
        return [
            'weight_to_area' => 'Weight to Area (kg → sqm)',
            'area_to_weight' => 'Area to Weight (sqm → kg)',
            'custom' => 'Custom Conversion'
        ];
    }

    public static function getCommonUnits()
    {
        return [
            'kg' => 'Kilograms (kg)',
            'sqm' => 'Square Meters (sqm)',
            'pieces' => 'Pieces (pcs)',
            'meters' => 'Meters (m)',
            'liters' => 'Liters (L)',
            'boxes' => 'Boxes',
            'rolls' => 'Rolls'
        ];
    }

    public static function getCommonMicrons()
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
}
