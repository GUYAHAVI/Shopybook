<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'code',
        'discount_type',
        'discount_value',
        'minimum_amount',
        'start_date',
        'end_date',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function usage()
    {
        return $this->hasMany(PromotionUsage::class);
    }

    public function isActive()
    {
        return $this->is_active && 
               now()->between($this->start_date, $this->end_date) &&
               (!$this->usage_limit || $this->used_count < $this->usage_limit);
    }

    public function calculateDiscount($amount)
    {
        if ($this->discount_type === 'percentage') {
            return ($amount * $this->discount_value) / 100;
        }
        
        return min($this->discount_value, $amount);
    }
}
