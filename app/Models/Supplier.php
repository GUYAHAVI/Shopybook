<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'company_registration',
        'tax_number',
        'payment_terms',
        'credit_limit',
        'notes',
        'status',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the business that owns the supplier.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the products from this supplier.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the purchase orders from this supplier.
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Scope a query to only include active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include suppliers for a specific business.
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Get the supplier's full address.
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([$this->address, $this->city, $this->country]);
        return implode(', ', $parts);
    }

    /**
     * Get the supplier's display name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->contact_person ? "{$this->name} ({$this->contact_person})" : $this->name;
    }
}
