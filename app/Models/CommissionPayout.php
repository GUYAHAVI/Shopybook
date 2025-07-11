<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id', 'staff_id', 'amount', 'paid_at', 'notes'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
} 