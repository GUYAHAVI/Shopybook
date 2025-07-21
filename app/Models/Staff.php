<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id', 'name', 'role', 'commission_rate', 'contact'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function serviceRecords()
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function serviceItems()
    {
        return $this->hasMany(ServiceItem::class);
    }

    public function commissionPayouts()
    {
        return $this->hasMany(CommissionPayout::class);
    }

    // Calculate total commission earned from service items
    public function getTotalCommissionAttribute()
    {
        return $this->serviceItems()->sum('commission_amount') ?? 0;
    }

    // Calculate commission for a specific date range
    public function getCommissionForPeriod($startDate, $endDate)
    {
        return $this->serviceItems()
            ->whereHas('serviceBooking', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->sum('commission_amount') ?? 0;
    }

    // Get commission earned today
    public function getTodayCommissionAttribute()
    {
        return $this->getCommissionForPeriod(
            now()->startOfDay(),
            now()->endOfDay()
        );
    }

    // Get commission earned this month
    public function getThisMonthCommissionAttribute()
    {
        return $this->getCommissionForPeriod(
            now()->startOfMonth(),
            now()->endOfMonth()
        );
    }
} 