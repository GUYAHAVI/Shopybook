<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'category',
        'unit_type',
        'unit_cost',
        'current_quantity',
        'minimum_quantity',
        'maximum_quantity',
        'supplier',
        'brand',
        'model',
        'purchase_date',
        'expiry_date',
        'status',
        'storage_location',
        'notes'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expiry_date' => 'date',
        'unit_cost' => 'decimal:2',
        'current_quantity' => 'integer',
        'minimum_quantity' => 'integer',
        'maximum_quantity' => 'integer',
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    // Scopes
    public function scopeLowStock(Builder $query)
    {
        return $query->whereRaw('current_quantity <= minimum_quantity');
    }

    public function scopeOutOfStock(Builder $query)
    {
        return $query->where('current_quantity', 0);
    }

    public function scopeExpiringSoon(Builder $query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '<=', now()->addDays($days));
    }

    public function scopeByCategory(Builder $query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors & Mutators
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'bg-success',
            'low_stock' => 'bg-warning',
            'out_of_stock' => 'bg-danger',
            'discontinued' => 'bg-secondary'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function getIsLowStockAttribute()
    {
        return $this->current_quantity <= $this->minimum_quantity;
    }

    public function getIsOutOfStockAttribute()
    {
        return $this->current_quantity == 0;
    }

    public function getIsExpiringSoonAttribute()
    {
        if (!$this->expiry_date) return false;
        return $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function getTotalValueAttribute()
    {
        return $this->current_quantity * $this->unit_cost;
    }

    // Methods
    public function updateQuantity($quantity, $type = 'adjustment', $notes = null, $staffId = null)
    {
        $oldQuantity = $this->current_quantity;
        $newQuantity = $oldQuantity + $quantity;
        
        // Prevent negative quantities
        if ($newQuantity < 0) {
            $newQuantity = 0;
            $quantity = -$oldQuantity;
        }

        $this->update(['current_quantity' => $newQuantity]);

        // Update status based on new quantity
        $this->updateStatus();

        // Create transaction record
        InventoryTransaction::create([
            'business_id' => $this->business_id,
            'inventory_item_id' => $this->id,
            'staff_id' => $staffId,
            'transaction_type' => $type,
            'quantity' => $quantity,
            'unit_cost' => $this->unit_cost,
            'total_cost' => abs($quantity) * $this->unit_cost,
            'notes' => $notes,
            'transaction_date' => now()->toDateString(),
        ]);

        return $this;
    }

    public function updateStatus()
    {
        if ($this->current_quantity == 0) {
            $this->update(['status' => 'out_of_stock']);
        } elseif ($this->current_quantity <= $this->minimum_quantity) {
            $this->update(['status' => 'low_stock']);
        } else {
            $this->update(['status' => 'active']);
        }
    }

    public static function getCategories()
    {
        return [
            'equipment' => 'Equipment (Machines, Tools)',
            'consumable' => 'Consumables (Products, Supplies)',
            'cleaning' => 'Cleaning Supplies',
            'safety' => 'Safety Equipment',
            'furniture' => 'Furniture & Fixtures',
            'other' => 'Other Items'
        ];
    }

    public static function getUnitTypes()
    {
        return [
            'pieces' => 'Pieces (pcs)',
            'bottles' => 'Bottles',
            'liters' => 'Liters (L)',
            'milliliters' => 'Milliliters (ml)',
            'kilograms' => 'Kilograms (kg)',
            'grams' => 'Grams (g)',
            'meters' => 'Meters (m)',
            'sets' => 'Sets',
            'boxes' => 'Boxes',
            'packs' => 'Packs'
        ];
    }
}
