<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quotation extends Model
{
    use HasFactory;

    public const STATUSES = [
        'draft'     => 'Draft',
        'sent'      => 'Sent to Customer',
        'accepted'  => 'Accepted',
        'declined'  => 'Declined',
        'converted' => 'Converted to Sale',
        'cancelled' => 'Cancelled',
    ];

    protected $fillable = [
        'business_id',
        'quote_number',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'status',
        'quote_date',
        'valid_until',
        'subtotal',
        'tax',
        'total_amount',
        'notes',
        'created_by',
        'order_id',
    ];

    protected $casts = [
        'quote_date'  => 'date',
        'valid_until' => 'date',
        'subtotal'    => 'decimal:2',
        'tax'         => 'decimal:2',
        'total_amount'=> 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (empty($quote->quote_number)) {
                $quote->quote_number = self::generateQuoteNumber();
            }
        });
    }

    public static function generateQuoteNumber()
    {
        $prefix = 'QT-';
        $date = date('Ymd');
        $last = self::where('quote_number', 'like', $prefix . $date . '%')
            ->orderBy('quote_number', 'desc')
            ->first();

        $sequence = $last ? intval(substr($last->quote_number, -4)) + 1 : 1;
        return $prefix . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Display name — linked customer, else the snapshot fields. */
    public function getCustomerDisplayNameAttribute(): string
    {
        return $this->customer?->name ?: ($this->customer_name ?: '—');
    }

    /** Best email on record for this quote. */
    public function getContactEmailAttribute(): ?string
    {
        return $this->customer?->email ?: $this->customer_email;
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft'     => 'secondary',
            'sent'      => 'info',
            'accepted'  => 'success',
            'declined'  => 'danger',
            'converted' => 'dark',
            'cancelled' => 'secondary',
            default     => 'secondary',
        };
    }

    /** Sent + past its validity date — still convertible, but flag it. */
    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until
            && $this->valid_until->isPast()
            && in_array($this->status, ['sent', 'accepted'], true);
    }

    public function getEditableAttribute(): bool
    {
        return $this->status === 'draft';
    }

    /** Can this quote be turned into a sale? */
    public function getConvertibleAttribute(): bool
    {
        return in_array($this->status, ['draft', 'sent', 'accepted'], true)
            && !$this->order_id;
    }

    public function getCancellableAttribute(): bool
    {
        return !in_array($this->status, ['converted', 'cancelled'], true);
    }
}
