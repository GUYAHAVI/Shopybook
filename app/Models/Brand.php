<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'logo_path',
        'website_url',
        'contact_email',
        'contact_phone',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
        'slug',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the business that owns the brand.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the products for this brand.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope a query to only include active brands.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include brands for a specific business.
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Scope a query to order brands by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Check if this brand has any products.
     */
    public function hasProducts()
    {
        return $this->products()->exists();
    }

    /**
     * Get the brand's display name with product count.
     */
    public function getDisplayNameAttribute()
    {
        $productCount = $this->products()->count();
        return $productCount > 0 ? "{$this->name} ({$productCount} products)" : $this->name;
    }

    /**
     * Generate URL-friendly slug from name.
     */
    public function generateSlug()
    {
        return \Str::slug($this->name);
    }

    /**
     * Get the brand's logo URL or default.
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo_path 
            ? asset('storage/' . $this->logo_path)
            : asset('images/default-brand-logo.png');
    }
}
