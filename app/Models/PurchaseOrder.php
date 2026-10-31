<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory;

    public const STATUSES = [
        'draft' => 'Draft',
        'sent' => 'Sent to Supplier',
        'partially_received' => 'Partially Received',
        'received' => 'Fully Received',
        'cancelled' => 'Cancelled',
    ];

    protected $fillable = [
        'business_id',
        'po_number',
        'supplier_id',
        'status',
        'order_date',
        'expected_date',
        'total_amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($po) {
            if (empty($po->po_number)) {
                $po->po_number = self::generatePoNumber();
            }
        });
    }

    public static function generatePoNumber()
    {
        $prefix = 'PO-';
        $date = date('Ymd');
        $last = self::where('po_number', 'like', $prefix . $date . '%')
            ->orderBy('po_number', 'desc')
            ->first();

        $sequence = $last ? intval(substr($last->po_number, -4)) + 1 : 1;
        return $prefix . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function receipts()
    {
        return $this->hasMany(StockReceipt::class);
    }

    public function debts()
    {
        return $this->hasMany(SupplierDebt::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'sent' => 'info',
            'partially_received' => 'warning',
            'received' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getReceivedPercentAttribute()
    {
        $ordered = $this->items->sum('quantity_ordered');
        if ($ordered <= 0) {
            return 0;
        }
        return min(100, round($this->items->sum('quantity_received') / $ordered * 100));
    }

    /** Recompute status from the items' received quantities (keeps draft/sent/cancelled logic intact). */
    public function refreshStatus(): void
    {
        if (in_array($this->status, ['draft', 'cancelled'], true)) {
            return;
        }

        $ordered = (float) $this->items()->sum('quantity_ordered');
        $received = (float) $this->items()->sum('quantity_received');

        $this->status = $ordered > 0 && $received >= $ordered
            ? 'received'
            : ($received > 0 ? 'partially_received' : 'sent');
        $this->save();
    }

    public function getEditableAttribute(): bool
    {
        return $this->status === 'draft';
    }

    public function getReceivableAttribute(): bool
    {
        return in_array($this->status, ['sent', 'partially_received'], true)
            && $this->items->contains(fn ($i) => $i->remaining > 0);
    }
}
