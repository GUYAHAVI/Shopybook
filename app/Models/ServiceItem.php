<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_record_id',
        'service_booking_id',
        'service_id',
        'staff_id',
        'amount',
        'commission_rate',
        'commission_amount',
        'sequence_order',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2'
    ];

    public function serviceBooking()
    {
        return $this->belongsTo(ServiceBooking::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function getFormattedAmountAttribute()
    {
        return 'KSh ' . number_format((float)$this->amount, 2);
    }

    public function getFormattedCommissionAttribute()
    {
        return 'KSh ' . number_format((float)$this->commission_amount, 2);
    }
}
