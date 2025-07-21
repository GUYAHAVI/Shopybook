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
        'total_amount',
        'payment_status',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'service_date' => 'datetime',
        'total_amount' => 'decimal:2'
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
}
