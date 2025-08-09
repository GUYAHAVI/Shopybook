<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'staff_id',
        'amount',
        'advance_date',
        'status',
        'deduction_status',
        'remaining_balance',
        'reason',
        'notes',
        'approved_by',
        'approved_at',
        'paid_at',
    ];

    protected static function boot()
    {
        parent::boot();
        
        // Clear staff cache when salary advances are created, updated, or deleted
        static::saved(function ($salaryAdvance) {
            if ($salaryAdvance->staff) {
                $salaryAdvance->staff->clearCalculatedAttributesCache();
            }
        });
        
        static::deleted(function ($salaryAdvance) {
            if ($salaryAdvance->staff) {
                $salaryAdvance->staff->clearCalculatedAttributesCache();
            }
        });
    }

    protected $casts = [
        'amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'advance_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Accessors and Mutators
    public function getFormattedAmountAttribute()
    {
        return 'KSh ' . number_format($this->amount, 2);
    }

    public function getFormattedRemainingBalanceAttribute()
    {
        return 'KSh ' . number_format($this->remaining_balance, 2);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'approved' => '<span class="badge bg-info">Approved</span>',
            'paid' => '<span class="badge bg-success">Paid</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getDeductionStatusBadgeAttribute()
    {
        return match($this->deduction_status) {
            'pending' => '<span class="badge bg-warning">Pending Deduction</span>',
            'partial' => '<span class="badge bg-info">Partially Deducted</span>',
            'deducted' => '<span class="badge bg-success">Fully Deducted</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    // Business Logic Methods
    public function approve($userId = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'remaining_balance' => $this->amount,
            'paid_at' => now(),
        ]);
    }

    public function deductFromSalary($amount)
    {
        $deductedAmount = min($amount, $this->remaining_balance);
        $newBalance = $this->remaining_balance - $deductedAmount;
        
        $this->update([
            'remaining_balance' => $newBalance,
            'deduction_status' => $newBalance <= 0 ? 'deducted' : 'partial',
        ]);

        return $deductedAmount;
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    public function isFullyDeducted()
    {
        return $this->remaining_balance <= 0;
    }

    public function canBeDeducted()
    {
        return $this->status === 'paid' && $this->remaining_balance > 0;
    }

    // Scopes
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePendingDeduction($query)
    {
        return $query->where('deduction_status', 'pending')
                    ->where('status', 'paid');
    }
}
