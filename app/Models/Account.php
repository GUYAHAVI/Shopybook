<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public const ASSET = 'asset';
    public const LIABILITY = 'liability';
    public const EQUITY = 'equity';
    public const INCOME = 'income';
    public const EXPENSE = 'expense';

    public const TYPES = [self::ASSET, self::LIABILITY, self::EQUITY, self::INCOME, self::EXPENSE];

    protected $fillable = [
        'business_id',
        'parent_id',
        'code',
        'name',
        'type',
        'sub_type',
        'system_key',
        'is_group',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function lines()
    {
        return $this->hasMany(JournalLine::class);
    }

    /** Assets and expenses grow with debits; liabilities, equity and income grow with credits.
     *  Contra accounts (e.g. sales returns, owner drawings) run the other way. */
    public function isDebitNormal(): bool
    {
        $normal = in_array($this->type, [self::ASSET, self::EXPENSE], true);

        return $this->sub_type === 'contra' ? !$normal : $normal;
    }

    /** Balance in the account's natural direction (positive = normal). */
    public function balance(?string $asOf = null, ?string $from = null): float
    {
        $totals = $this->lines()
            ->whereHas('entry', fn ($q) => $q->where('status', JournalEntry::POSTED))
            ->when($from, fn ($q) => $q->where('posting_date', '>=', $from))
            ->when($asOf, fn ($q) => $q->where('posting_date', '<=', $asOf))
            ->selectRaw('COALESCE(SUM(debit),0) as dr, COALESCE(SUM(credit),0) as cr')
            ->first();

        $net = (float) $totals->dr - (float) $totals->cr;

        return round($this->isDebitNormal() ? $net : -$net, 2);
    }
}
