<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatementLine extends Model
{
    protected $fillable = ['business_id', 'account_id', 'txn_date', 'amount', 'reference', 'memo', 'journal_line_id', 'matched'];

    protected $casts = ['txn_date' => 'date', 'amount' => 'float', 'matched' => 'boolean'];
}
