<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'inventory_item_id',
        'staff_id',
        'transaction_type',
        'quantity',
        'unit_cost',
        'total_cost',
        'reference_number',
        'notes',
        'transaction_date',
        'service_booking_id'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function serviceBooking()
    {
        return $this->belongsTo(ServiceBooking::class);
    }

    // Accessors
    public function getTransactionTypeBadgeAttribute()
    {
        $badges = [
            'purchase' => 'bg-success',
            'usage' => 'bg-primary',
            'wastage' => 'bg-danger',
            'return' => 'bg-warning',
            'transfer' => 'bg-info',
            'adjustment' => 'bg-secondary',
            'repair_out' => 'bg-warning',
            'repair_in' => 'bg-success',
            'sale' => 'bg-success'
        ];

        return $badges[$this->transaction_type] ?? 'bg-secondary';
    }

    public function getIsAdditionAttribute()
    {
        return in_array($this->transaction_type, ['purchase', 'return', 'repair_in', 'adjustment']) && $this->quantity > 0;
    }

    public function getIsSubtractionAttribute()
    {
        return in_array($this->transaction_type, ['usage', 'wastage', 'transfer', 'repair_out', 'sale']) || $this->quantity < 0;
    }

    // Static methods
    public static function getTransactionTypes()
    {
        return [
            'purchase' => 'Purchase (New Stock)',
            'usage' => 'Used in Service',
            'wastage' => 'Wastage/Damage/Loss',
            'return' => 'Return to Supplier',
            'transfer' => 'Transfer Location',
            'adjustment' => 'Manual Adjustment',
            'repair_out' => 'Sent for Repair',
            'repair_in' => 'Returned from Repair',
            'sale' => 'Sold as Retail'
        ];
    }
}


