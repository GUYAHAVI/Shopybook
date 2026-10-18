<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaStkRequest extends Model
{
    protected $fillable = [
        'business_id',
        'order_id',
        'rental_payment_id',
        'subscription_payment_id',
        'phone',
        'amount',
        'transaction_type',
        'party_b',
        'account_reference',
        'merchant_request_id',
        'checkout_request_id',
        'status',
        'result_code',
        'result_desc',
        'mpesa_receipt_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'result_code' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
