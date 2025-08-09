<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id', 'name', 'role', 'salary', 'contact'
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
        // Use eagerly loaded relationship if available, otherwise query
        if ($this->relationLoaded('serviceItems')) {
            return $this->serviceItems->sum('commission_amount') ?? 0;
        }
        return $this->serviceItems()->sum('commission_amount') ?? 0;
    }

    // Calculate total earnings (salary + commission)
    public function getTotalEarningsAttribute()
    {
        $commission = $this->total_commission;
        return ($this->salary ?? 0) + $commission;
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

    // Calculate total earnings (salary + commission) for a specific date range
    public function getEarningsForPeriod($startDate, $endDate)
    {
        $commission = $this->getCommissionForPeriod($startDate, $endDate);
        return ($this->salary ?? 0) + $commission;
    }

    // Get total earnings (salary + commission) for today
    public function getTodayEarningsAttribute()
    {
        return $this->getEarningsForPeriod(
            now()->startOfDay(),
            now()->endOfDay()
        );
    }

    // Get total earnings (salary + commission) for this month
    public function getThisMonthEarningsAttribute()
    {
        return $this->getEarningsForPeriod(
            now()->startOfMonth(),
            now()->endOfMonth()
        );
    }

    // Salary Advance Relationships
    public function salaryAdvances()
    {
        return $this->hasMany(SalaryAdvance::class);
    }

    // Salary Advance Calculation Methods
    public function getTotalAdvancesAttribute()
    {
        // Use eagerly loaded relationship if available, otherwise query
        if ($this->relationLoaded('salaryAdvances')) {
            return $this->salaryAdvances->where('status', 'paid')->sum('amount');
        }
        return $this->salaryAdvances()->paid()->sum('amount');
    }

    public function getPendingAdvanceDeductionsAttribute()
    {
        // Use eagerly loaded relationship if available, otherwise query
        if ($this->relationLoaded('salaryAdvances')) {
            return $this->salaryAdvances
                ->where('status', 'paid')
                ->where('deduction_status', '!=', 'deducted')
                ->sum('remaining_balance');
        }
        return $this->salaryAdvances()->pendingDeduction()->sum('remaining_balance');
    }

    public function getNetSalaryAfterAdvancesAttribute()
    {
        $salary = $this->salary ?? 0;
        $pendingDeductions = $this->pending_advance_deductions;
        return max(0, $salary - $pendingDeductions);
    }

    public function getAdvancesForMonth($year, $month)
    {
        return $this->salaryAdvances()
            ->whereYear('advance_date', $year)
            ->whereMonth('advance_date', $month)
            ->paid()
            ->get();
    }

    public function getTotalAdvancesForMonth($year, $month)
    {
        return $this->getAdvancesForMonth($year, $month)->sum('amount');
    }

    public function canTakeAdvance($amount)
    {
        $salary = $this->salary ?? 0;
        $currentPendingDeductions = $this->pending_advance_deductions;
        $availableAmount = $salary - $currentPendingDeductions;
        
        return $amount <= $availableAmount;
    }

    public function getAvailableAdvanceAmountAttribute()
    {
        $salary = $this->salary ?? 0;
        $pendingDeductions = $this->pending_advance_deductions;
        return max(0, $salary - $pendingDeductions);
    }

    // Calculate net salary considering commission and advance deductions
    public function getNetMonthlySalaryAttribute()
    {
        $baseSalary = $this->salary ?? 0;
        $monthlyCommission = $this->this_month_commission;
        $pendingDeductions = $this->pending_advance_deductions;
        
        return max(0, $baseSalary + $monthlyCommission - $pendingDeductions);
    }

    public function processMonthlyAdvanceDeductions()
    {
        $advancesToDeduct = $this->salaryAdvances()->pendingDeduction()->get();
        $totalDeducted = 0;
        $availableAmount = $this->salary ?? 0;

        foreach ($advancesToDeduct as $advance) {
            if ($availableAmount <= 0) break;
            
            $deductedAmount = $advance->deductFromSalary($availableAmount);
            $totalDeducted += $deductedAmount;
            $availableAmount -= $deductedAmount;
        }

        return $totalDeducted;
    }

    /**
     * Clear cached calculated attributes
     */
    public function clearCalculatedAttributesCache()
    {
        // Clear any cached attributes that might be stale
        if (isset($this->attributes['total_commission'])) {
            unset($this->attributes['total_commission']);
        }
        if (isset($this->attributes['total_advances'])) {
            unset($this->attributes['total_advances']);
        }
        if (isset($this->attributes['pending_advance_deductions'])) {
            unset($this->attributes['pending_advance_deductions']);
        }
        if (isset($this->attributes['available_advance_amount'])) {
            unset($this->attributes['available_advance_amount']);
        }
        
        // Refresh relationships if needed
        $this->unsetRelation('salaryAdvances');
        $this->unsetRelation('serviceItems');
        
        return $this;
    }
} 