<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'customer_id',
        'order_id',
        'discount_amount',
        'usage_type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'discount_amount' => 'decimal:2',
    ];

    /**
     * Get the promotion that was used.
     */
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * Get the customer who used the promotion.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order where the promotion was used.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
