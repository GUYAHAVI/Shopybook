<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'business_id',
        'payment_number',
        'payment_method',
        'amount',
        'currency',
        'transaction_id',
        'transaction_reference',
        'status',
        'gateway_response',
        'notes',
        'received_by',
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
        return $symbol . number_format((float)$this->amount, 2);
    }

    public static function generatePaymentNumber()
    {
        $prefix = 'PAY';
        $year = now()->format('Y');
        $month = now()->format('m');
        
        $lastPayment = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastPayment ? (intval(substr($lastPayment->payment_number, -4)) + 1) : 1;
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $number);
    }
}
