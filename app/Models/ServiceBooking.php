<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'business_id',
        'service_date',
        'total_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'subtotal',
        'final_amount',
        'payment_status',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'service_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'final_amount' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($serviceBooking) {
            if (!$serviceBooking->service_date) {
                $serviceBooking->service_date = now();
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function serviceItems()
    {
        return $this->hasMany(ServiceItem::class, 'service_booking_id');
    }

    public function getTotalAmountAttribute($value)
    {
        return $this->serviceItems()->sum('amount') ?: $value;
    }

    public function getFormattedTotalAttribute()
    {
        return 'KSh ' . number_format((float)$this->total_amount, 2);
    }

    // Calculate discount amount based on type and value
    public function calculateDiscountAmount($subtotal = null)
    {
        $subtotal = $subtotal ?? $this->subtotal ?? $this->total_amount;
        
        if ($this->discount_type === 'none' || !$this->discount_value) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return ($subtotal * $this->discount_value) / 100;
        }

        if ($this->discount_type === 'fixed') {
            return min($this->discount_value, $subtotal); // Don't exceed subtotal
        }

        return 0;
    }

    // Calculate final amount after discount
    public function calculateFinalAmount($subtotal = null)
    {
        $subtotal = $subtotal ?? $this->subtotal ?? $this->total_amount;
        $discountAmount = $this->calculateDiscountAmount($subtotal);
        return max(0, $subtotal - $discountAmount); // Don't go below 0
    }

    // Get formatted discount amount
    public function getFormattedDiscountAmountAttribute()
    {
        return 'KSh ' . number_format((float)($this->discount_amount ?? 0), 2);
    }

    // Get formatted subtotal
    public function getFormattedSubtotalAttribute()
    {
        return 'KSh ' . number_format((float)($this->subtotal ?? $this->total_amount), 2);
    }

    // Get formatted final amount
    public function getFormattedFinalAmountAttribute()
    {
        return 'KSh ' . number_format((float)($this->final_amount ?? $this->total_amount), 2);
    }

    // Get discount display text
    public function getDiscountDisplayAttribute()
    {
        if ($this->discount_type === 'none' || !$this->discount_value) {
            return 'No discount';
        }

        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        }

        if ($this->discount_type === 'fixed') {
            return 'KSh ' . number_format((float)$this->discount_value, 2);
        }

        return 'No discount';
    }
}
