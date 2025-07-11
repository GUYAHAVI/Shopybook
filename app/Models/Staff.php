<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id', 'name', 'role', 'commission_rate', 'contact'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function serviceRecords()
    {
        return $this->hasMany(ServiceRecord::class);
    }

    public function commissionPayouts()
    {
        return $this->hasMany(CommissionPayout::class);
    }
} 