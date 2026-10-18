<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalTenant extends Model
{
    protected $fillable = [
        'business_id',
        'rental_unit_id',
        'name',
        'phone',
        'email',
        'id_number',
        'rent_amount',
        'status',
        'move_in_date',
        'move_out_date',
        'notes',
    ];

    protected $casts = [
        'rent_amount'   => 'decimal:2',
        'move_in_date'  => 'date',
        'move_out_date' => 'date',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function unit()
    {
        return $this->belongsTo(RentalUnit::class, 'rental_unit_id');
    }

    public function payments()
    {
        return $this->hasMany(RentalPayment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /** Total paid for a period ('YYYY-MM'). */
    public function paidFor(string $period): float
    {
        return (float) $this->payments()
            ->where('period', $period)
            ->where('status', 'paid')
            ->sum('amount');
    }

    /** Outstanding balance for a period. */
    public function balanceFor(string $period): float
    {
        return max(0.0, (float) $this->rent_amount - $this->paidFor($period));
    }

    /**
     * Whether this tenant's paid payments fully cover rent for a period ('YYYY-MM').
     */
    public function hasPaidFor(string $period): bool
    {
        return $this->paidFor($period) >= (float) $this->rent_amount && (float) $this->rent_amount > 0;
    }
}
