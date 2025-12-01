<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'product_id',
        'receipt_number',
        'product_name',
        'supplier',
        'quantity_received',
        'unit_cost',
        'total_cost',
        'receipt_date',
        'invoice_number',
        'notes',
        'received_by',
        'receipt_type',
        'additional_data',
    ];

    protected $casts = [
        'quantity_received' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'receipt_date' => 'date',
        'additional_data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receipt) {
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = self::generateReceiptNumber();
            }
        });
    }

    /**
     * Generate unique receipt number
     */
    public static function generateReceiptNumber()
    {
        $prefix = 'RCV';
        $date = date('Ymd');
        $lastReceipt = self::where('receipt_number', 'like', $prefix . $date . '%')
            ->orderBy('receipt_number', 'desc')
            ->first();

        if ($lastReceipt) {
            $lastNumber = intval(substr($lastReceipt->receipt_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . $newNumber;
    }

    /**
     * Get the business that owns the stock receipt
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the product associated with this receipt
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who received the stock
     */
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get formatted total cost
     */
    public function getFormattedTotalCostAttribute()
    {
        return $this->total_cost ? 'KSh ' . number_format((float)$this->total_cost, 2) : 'N/A';
    }

    /**
     * Get formatted unit cost
     */
    public function getFormattedUnitCostAttribute()
    {
        return $this->unit_cost ? 'KSh ' . number_format((float)$this->unit_cost, 2) : 'N/A';
    }

    /**
     * Scope for existing product receipts
     */
    public function scopeExistingProducts($query)
    {
        return $query->where('receipt_type', 'existing_product');
    }

    /**
     * Scope for new product receipts
     */
    public function scopeNewProducts($query)
    {
        return $query->where('receipt_type', 'new_product');
    }

    /**
     * Scope for receipts by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('receipt_date', [$startDate, $endDate]);
    }
}

