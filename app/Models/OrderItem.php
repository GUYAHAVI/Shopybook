<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'total',
        'sell_unit',
        'material_type',
        'original_quantity',
        'converted_quantity',
        'converted_unit',
        'price_per_unit',
        'conversion_data',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
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
}
