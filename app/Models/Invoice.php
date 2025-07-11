<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'business_id',
        'order_id',
        'invoice_number',
        'amount',
        'status',
        'due_date',
        'paid_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'warning';
            case 'paid':
                return 'success';
            case 'overdue':
                return 'danger';
            case 'cancelled':
                return 'secondary';
            default:
                return 'secondary';
        }
    }

    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    public function getFormattedAmountAttribute()
    {
        return 'KSh ' . number_format($this->amount, 2);
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }
}
