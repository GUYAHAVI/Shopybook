<?php

namespace App\Models;

use App\Exceptions\Accounting\LedgerException;
use Illuminate\Database\Eloquent\Model;

class JournalLine extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'journal_entry_id',
        'business_id',
        'account_id',
        'posting_date',
        'debit',
        'credit',
        'party_type',
        'party_id',
        'memo',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        $guard = function (JournalLine $line) {
            if ($line->entry?->status === JournalEntry::POSTED) {
                throw new LedgerException('Lines of a posted journal entry cannot be changed.');
            }
        };

        static::updating($guard);
        static::deleting($guard);
    }

    public function entry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
