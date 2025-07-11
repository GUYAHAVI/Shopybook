<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id', 'name', 'price', 'duration', 'commission_rate', 'description'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function serviceRecords()
    {
        return $this->hasMany(ServiceRecord::class);
    }
} 