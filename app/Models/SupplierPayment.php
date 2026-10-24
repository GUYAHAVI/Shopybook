<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $fillable = [
        'business_id',
        'supplier_debt_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'notes',
        'paid_at',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function debt()
    {
        return $this->belongsTo(SupplierDebt::class, 'supplier_debt_id');
    }
}
