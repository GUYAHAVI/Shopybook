<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalPayment extends Model
{
    protected $fillable = [
        'business_id',
        'rental_tenant_id',
        'rental_unit_id',
        'period',
        'amount',
        'method',
        'status',
        'transaction_reference',
        'mpesa_stk_request_id',
        'paid_at',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function tenant()
    {
        return $this->belongsTo(RentalTenant::class, 'rental_tenant_id');
    }

    public function unit()
    {
        return $this->belongsTo(RentalUnit::class, 'rental_unit_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function stkRequest()
    {
        return $this->belongsTo(MpesaStkRequest::class, 'mpesa_stk_request_id');
    }
}
