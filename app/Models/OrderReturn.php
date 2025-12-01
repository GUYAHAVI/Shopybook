<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'business_id',
        'order_id',
        'customer_id',
        'return_number',
        'return_type',
        'status',
        'reason',
        'reason_category',
        'original_amount',
        'refund_amount',
        'restocking_fee',
        'refund_method',
        'refund_processed',
        'refund_processed_at',
        'return_to_stock',
        'stock_returned',
        'items_data',
        'processed_by',
        'processed_at',
        'notes',
        'internal_notes',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'restocking_fee' => 'decimal:2',
        'refund_processed' => 'boolean',
        'refund_processed_at' => 'datetime',
        'return_to_stock' => 'boolean',
        'stock_returned' => 'boolean',
        'items_data' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Generate unique return number on creation
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($return) {
            if (empty($return->return_number)) {
                $return->return_number = 'RET-' . now()->format('Ymd') . '-' . str_pad(
                    OrderReturn::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    /**
     * Relationships
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Accessor methods
     */
    public function getFormattedRefundAmountAttribute()
    {
        return 'KSh ' . number_format((float) $this->refund_amount, 2);
    }

    public function getFormattedOriginalAmountAttribute()
    {
        return 'KSh ' . number_format((float) $this->original_amount, 2);
    }

    public function getFormattedRestockingFeeAttribute()
    {
        return 'KSh ' . number_format((float) $this->restocking_fee, 2);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'completed' => 'success',
            default => 'secondary',
        };
    }

    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    public function getReasonCategoryTextAttribute()
    {
        return str_replace('_', ' ', ucwords($this->reason_category, '_'));
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }
}
