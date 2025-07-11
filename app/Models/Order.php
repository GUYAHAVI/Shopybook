<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'business_id',
        'customer_id',
        'order_number',
        'status',
        'payment_method',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'warning';
            case 'processing':
                return 'info';
            case 'completed':
                return 'success';
            case 'cancelled':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    public function getFormattedTotalAttribute()
    {
        return 'KSh ' . number_format($this->total_amount, 2);
    }
}
