<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'currency',
        'transaction_id',
        'status',
        'gateway_response',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            'refunded' => 'info',
            default => 'secondary'
        };
    }

    public function getPaymentMethodIconAttribute()
    {
        return match($this->payment_method) {
            'mpesa' => 'fas fa-mobile-alt',
            'paypal' => 'fab fa-paypal',
            'card' => 'fas fa-credit-card',
            'cash' => 'fas fa-money-bill-wave',
            default => 'fas fa-money-bill'
        };
    }

    public function getFormattedAmountAttribute()
    {
        $symbols = [
            'KES' => '₦',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;
        return $symbol . number_format($this->amount, 2);
    }
}
