<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'stock_qty',
        'unit_cost',
        'cost_amount',
        'price',
        'total',
        'tax_amount',
        'tax_treatment',
        'unit',
        'sell_unit',
        'material_type',
        'original_quantity',
        'converted_quantity',
        'converted_unit',
        'price_per_unit',
        'conversion_data',
    ];

    protected $casts = [
        'quantity' => 'float',
        'stock_qty' => 'float',
        'unit_cost' => 'float',
        'cost_amount' => 'decimal:2',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'original_quantity' => 'decimal:4',
        'converted_quantity' => 'decimal:4',
        'price_per_unit' => 'decimal:2',
        'conversion_data' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Stock units (e.g. kg) that were deducted when this line was sold.
     * Converted sales (e.g. sold in sqm) deduct a different quantity than the line quantity.
     */
    public function stockQuantity(): float
    {
        if ($this->stock_qty !== null) {
            return (float) $this->stock_qty;
        }

        if ($this->original_quantity !== null) {
            if ($this->sell_unit === 'sqm' && $this->material_type && preg_match('/(\d+\.\d+)/', $this->material_type, $m)) {
                return (float) $this->original_quantity * (float) $m[1];
            }
            if ($this->sell_unit === 'kg') {
                return (float) $this->original_quantity;
            }
        }

        return (float) $this->quantity;
    }

    /** Stock units to put back when $quantity (in sold units) of this line is returned. */
    public function stockQuantityFor(float $quantity): float
    {
        $sold = (float) $this->quantity;

        return $sold > 0 ? round($this->stockQuantity() * min($quantity, $sold) / $sold, 3) : 0.0;
    }
}
