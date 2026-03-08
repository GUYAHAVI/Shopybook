<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessApp extends Model
{
    protected $fillable = [
        'business_id',
        'app_slug',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the business that owns the app
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get all available apps with their metadata
     */
    public static function availableApps(): array
    {
        return [
            'pos' => [
                'name' => 'Point of Sale',
                'icon' => 'fas fa-cash-register',
                'description' => 'Fast checkout and sales processing',
                'category' => 'core',
                'color' => 'success',
                'required_for' => ['product', 'service', 'hybrid'],
            ],
            'products' => [
                'name' => 'Products & Inventory',
                'icon' => 'fas fa-boxes',
                'description' => 'Manage products, stock levels, and inventory',
                'category' => 'core',
                'color' => 'primary',
                'required_for' => ['product', 'hybrid'],
            ],
            'sales' => [
                'name' => 'Sales & Orders',
                'icon' => 'fas fa-shopping-cart',
                'description' => 'Track orders, customers, and sales performance',
                'category' => 'core',
                'color' => 'info',
                'required_for' => ['product', 'service', 'hybrid'],
            ],
            'services' => [
                'name' => 'Services & Bookings',
                'icon' => 'fas fa-calendar-check',
                'description' => 'Manage services, bookings, and appointments',
                'category' => 'operations',
                'color' => 'warning',
                'required_for' => ['service', 'hybrid'],
            ],
            'customers' => [
                'name' => 'Customer Management',
                'icon' => 'fas fa-users',
                'description' => 'Build relationships with your customers',
                'category' => 'sales',
                'color' => 'info',
                'required_for' => ['product', 'service', 'hybrid'],
            ],
            'staff' => [
                'name' => 'Staff Management',
                'icon' => 'fas fa-user-tie',
                'description' => 'Manage employees, roles, and schedules',
                'category' => 'operations',
                'color' => 'secondary',
                'required_for' => ['service', 'hybrid'],
            ],
            'finance' => [
                'name' => 'Financial Management',
                'icon' => 'fas fa-chart-pie',
                'description' => 'Expenses, costs, taxes, and financial reports',
                'category' => 'finance',
                'color' => 'success',
                'required_for' => ['product', 'service', 'hybrid'],
            ],
            'suppliers' => [
                'name' => 'Supplier Management',
                'icon' => 'fas fa-truck',
                'description' => 'Track suppliers and purchase orders',
                'category' => 'operations',
                'color' => 'warning',
                'required_for' => ['product', 'hybrid'],
            ],
            'marketing' => [
                'name' => 'Marketing & Website',
                'icon' => 'fas fa-bullhorn',
                'description' => 'Build your website, social media, and campaigns',
                'category' => 'growth',
                'color' => 'danger',
                'required_for' => [],
            ],
            'ai_tools' => [
                'name' => 'AI Tools',
                'icon' => 'fas fa-robot',
                'description' => 'AI assistant, content enhancer, and automation',
                'category' => 'tools',
                'color' => 'info',
                'badge' => 'AI',
                'required_for' => [],
            ],
            'reports' => [
                'name' => 'Reports & Analytics',
                'icon' => 'fas fa-chart-line',
                'description' => 'Comprehensive business insights and reports',
                'category' => 'analytics',
                'color' => 'primary',
                'required_for' => [],
            ],
            'returns' => [
                'name' => 'Returns & Refunds',
                'icon' => 'fas fa-undo',
                'description' => 'Handle product returns and customer refunds',
                'category' => 'operations',
                'color' => 'danger',
                'required_for' => ['product', 'hybrid'],
            ],
        ];
    }

    /**
     * Get app metadata
     */
    public function getAppMetadata(): ?array
    {
        $apps = self::availableApps();
        return $apps[$this->app_slug] ?? null;
    }

    /**
     * Scope: Get only active apps
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by custom order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
