<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertisingCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'platform',
        'budget',
        'spent',
        'start_date',
        'end_date',
        'target_audience',
        'status',
        'metrics',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'metrics' => 'array',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function getProgressAttribute()
    {
        if ($this->budget <= 0) return 0;
        return min(100, ($this->spent / $this->budget) * 100);
    }

    public function getRemainingBudgetAttribute()
    {
        return max(0, $this->budget - $this->spent);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'active' => 'success',
            'paused' => 'warning',
            'completed' => 'info',
            default => 'secondary'
        };
    }
}
