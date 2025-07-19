<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'department',
        'hire_date',
        'salary',
        'hourly_rate',
        'employment_type',
        'status',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'datetime',
        'salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the business that owns the employee.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the employee's full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the employee's display name with position.
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->full_name} - {$this->position}";
    }

    /**
     * Scope a query to only include active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include employees for a specific business.
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope a query to filter by employment type.
     */
    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }
    /**
     * Get the employee's years of service.
     */
    public function getYearsOfServiceAttribute()
    {
        return $this->hire_date ? Carbon::parse($this->hire_date)->diffInYears(now()) : 0;
    }

    /**
     * Check if employee is on probation (less than 6 months).
     */
    public function getIsOnProbationAttribute()
    {
        return $this->hire_date && Carbon::parse($this->hire_date)->diffInMonths(now()) < 6;
    }
}
