<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id', 'type', 'amount', 'paid_from_account_id', 'description', 'date', 'salary_paid_at'
    ];

    public function paidFrom()
    {
        return $this->belongsTo(Account::class, 'paid_from_account_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
} 