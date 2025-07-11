<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getTotalSpentAttribute()
    {
        return $this->orders()->where('status', 'completed')->sum('total_amount');
    }

    public function getFormattedTotalSpentAttribute()
    {
        return 'KSh ' . number_format($this->total_spent, 2);
    }

    public function getOrderCountAttribute()
    {
        return $this->orders()->count();
    }

    public function getCompletedOrderCountAttribute()
    {
        return $this->orders()->where('status', 'completed')->count();
    }
}
