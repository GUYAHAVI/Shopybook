<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'slug',
        'description',
        'price',
        'cost_price',
        'sku',
        'barcode',
        'category',
        'brand',
        'stock_quantity',
        'low_stock_threshold',
        'weight',
        'dimensions',
        'images',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'tags',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'weight' => 'decimal:2',
        'images' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'tags' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
                    ->withPivot('quantity', 'price', 'total')
                    ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getMainImageAttribute()
    {
        return $this->images ? $this->images[0] : null;
    }

    public function getFormattedPriceAttribute()
    {
        return 'KSh ' . number_format($this->price, 2);
    }

    public function getFormattedCostPriceAttribute()
    {
        return $this->cost_price ? 'KSh ' . number_format($this->cost_price, 2) : 'N/A';
    }

    public function getProfitMarginAttribute()
    {
        if (!$this->cost_price || $this->cost_price == 0) {
            return null;
        }
        
        return (($this->price - $this->cost_price) / $this->cost_price) * 100;
    }

    public function getProfitMarginFormattedAttribute()
    {
        $margin = $this->profit_margin;
        return $margin ? number_format($margin, 1) . '%' : 'N/A';
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock_quantity <= $this->low_stock_threshold) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    public function getStockStatusTextAttribute()
    {
        switch ($this->stock_status) {
            case 'out_of_stock':
                return 'Out of Stock';
            case 'low_stock':
                return 'Low Stock';
            default:
                return 'In Stock';
        }
    }

    public function getStockStatusColorAttribute()
    {
        switch ($this->stock_status) {
            case 'out_of_stock':
                return 'danger';
            case 'low_stock':
                return 'warning';
            default:
                return 'success';
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->where('stock_quantity', '<=', 'low_stock_threshold')
                    ->where('stock_quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%");
        });
    }
} 