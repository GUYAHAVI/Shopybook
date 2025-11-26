<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $fillable = [
        'order_id',
        'credit_note_number',
        'invoice_number',
        'reason',
        'amount',
        'status',
        'otp_code',
        'otp_expires_at',
        'requested_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'otp_expires_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the order that owns the credit note.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who requested the credit note.
     */
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who approved the credit note.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if OTP is valid.
     */
    public function isOtpValid($otp)
    {
        return $this->otp_code === $otp && 
               $this->otp_expires_at && 
               now()->lessThan($this->otp_expires_at);
    }

    /**
     * Generate a unique credit note number.
     */
    public static function generateCreditNoteNumber()
    {
        $prefix = 'CN';
        $year = now()->format('Y');
        $month = now()->format('m');
        
        $lastCreditNote = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastCreditNote ? (intval(substr($lastCreditNote->credit_note_number, -4)) + 1) : 1;
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $number);
    }
}
