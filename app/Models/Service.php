<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id', 'name', 'price', 'duration', 'commission_rate', 'description', 'is_active',
        'is_bundle_trigger', 'bundled_services', 'is_complimentary', 'parent_service_id'
    ];

    protected $casts = [
        'bundled_services' => 'array',
        'is_bundle_trigger' => 'boolean',
        'is_complimentary' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function serviceItems()
    {
        return $this->hasMany(ServiceItem::class);
    }

    // Bundled services relationships
    public function parentService()
    {
        return $this->belongsTo(Service::class, 'parent_service_id');
    }

    public function childServices()
    {
        return $this->hasMany(Service::class, 'parent_service_id');
    }

    // Get bundled services as Service models
    public function getBundledServicesModels()
    {
        if (!$this->bundled_services) {
            return collect();
        }
        
        return Service::whereIn('id', $this->bundled_services)->get();
    }

    /**
     * Scope a query to only include active services.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 