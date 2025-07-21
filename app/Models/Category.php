<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'parent_id',
        'image_path',
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
     * Get the business that owns the category.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get all descendants (children, grandchildren, etc.)
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get the products in this category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include root categories (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to only include categories for a specific business.
     */
    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    /**
     * Get the category's full path (including parent categories).
     */
    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }

    /**
     * Check if this category has any child categories.
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    /**
     * Check if this category has any products.
     */
    public function hasProducts()
    {
        return $this->products()->exists();
    }

    /**
     * Generate URL-friendly slug from name.
     */
    public function generateSlug()
    {
        return \Str::slug($this->name);
    }
}
