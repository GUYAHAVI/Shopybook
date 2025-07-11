<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id', 'type', 'amount', 'description', 'date'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
} 