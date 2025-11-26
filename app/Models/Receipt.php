<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'order_id',
        'receipt_number',
        'receipt_data',
        'business_name',
        'customer_name',
        'customer_phone',
        'subtotal',
        'tax_amount',
        'total_amount',
        'payment_method',
        'currency_symbol',
        'is_converted_order'
    ];

    protected $casts = [
        'receipt_data' => 'array',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_converted_order' => 'boolean'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_name', 'name');
    }

    /**
     * Generate a unique receipt number
     */
    public static function generateReceiptNumber()
    {
        return 'RCP-' . strtoupper(uniqid());
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return $this->currency_symbol . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute()
    {
        return $this->currency_symbol . number_format($this->subtotal, 2);
    }

    /**
     * Get formatted tax amount
     */
    public function getFormattedTaxAttribute()
    {
        return $this->currency_symbol . number_format($this->tax_amount, 2);
    }
}
