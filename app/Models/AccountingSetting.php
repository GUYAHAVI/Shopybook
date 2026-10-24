<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingSetting extends Model
{
    protected $fillable = [
        'business_id',
        'ledger_start_date',
        'setup_mode',
        'setup_completed_at',
        'lock_date',
        'vat_registered',
        'fiscal_year_start_month',
        'payment_accounts',
        'next_invoice_number',
    ];

    protected $casts = [
        'ledger_start_date' => 'date',
        'setup_completed_at' => 'datetime',
        'lock_date' => 'date',
        'vat_registered' => 'boolean',
        'fiscal_year_start_month' => 'integer',
        'payment_accounts' => 'array',
        'next_entry_number' => 'integer',
        'next_invoice_number' => 'integer',
    ];

    /** Next invoice number for this business (call inside a transaction). */
    public function nextInvoiceNumber(): string
    {
        $n = $this->next_invoice_number ?: 1;
        $this->increment('next_invoice_number');

        return 'INV-'.date('Y').'-'.str_pad($n, 4, '0', STR_PAD_LEFT);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public static function for(string $businessId): self
    {
        return static::firstOrCreate(['business_id' => $businessId]);
    }
}
