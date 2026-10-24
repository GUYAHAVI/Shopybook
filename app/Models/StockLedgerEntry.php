<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLedgerEntry extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'business_id',
        'product_id',
        'posting_date',
        'qty_change',
        'valuation_rate',
        'value_change',
        'qty_after',
        'value_after',
        'source_type',
        'source_id',
        'journal_entry_id',
        'batch_id',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'qty_change' => 'float',
        'valuation_rate' => 'float',
        'value_change' => 'decimal:2',
        'qty_after' => 'float',
        'value_after' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
