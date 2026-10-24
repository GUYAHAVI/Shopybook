<?php

namespace App\Models;

use App\Exceptions\Accounting\LedgerException;
use Illuminate\Database\Eloquent\Model;

/**
 * A balanced accounting entry. Posted entries are immutable: corrections are made
 * with a reversing entry, never by editing or deleting.
 */
class JournalEntry extends Model
{
    public const DRAFT = 'draft';
    public const POSTED = 'posted';

    /** Attributes that may still change on a posted entry. */
    private const MUTABLE_WHEN_POSTED = ['reversed_by_id', 'updated_at'];

    protected $fillable = [
        'business_id',
        'entry_number',
        'posting_date',
        'description',
        'source_type',
        'source_id',
        'source_event',
        'reverses_id',
        'reversed_by_id',
        'status',
        'batch_id',
        'total',
        'created_by',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::updating(function (JournalEntry $entry) {
            if ($entry->getOriginal('status') !== self::POSTED) {
                return;
            }
            $changed = array_diff(array_keys($entry->getDirty()), self::MUTABLE_WHEN_POSTED);
            if ($changed) {
                throw new LedgerException('Posted journal entries cannot be edited; reverse them instead.');
            }
        });

        static::deleting(function (JournalEntry $entry) {
            if ($entry->getOriginal('status') === self::POSTED) {
                throw new LedgerException('Posted journal entries cannot be deleted; reverse them instead.');
            }
        });
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function lines()
    {
        return $this->hasMany(JournalLine::class);
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function reverses()
    {
        return $this->belongsTo(self::class, 'reverses_id');
    }

    public function reversedBy()
    {
        return $this->belongsTo(self::class, 'reversed_by_id');
    }

    public function scopePosted($query)
    {
        return $query->where('status', self::POSTED);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::DRAFT);
    }

    /** Entries that currently count: not reversed and not themselves reversals. */
    public function scopeActive($query)
    {
        return $query->whereNull('reversed_by_id')->whereNull('reverses_id');
    }

    public function isReversed(): bool
    {
        return $this->reversed_by_id !== null;
    }
}
