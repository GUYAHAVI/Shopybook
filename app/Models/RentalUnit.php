<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalUnit extends Model
{
    protected $fillable = [
        'business_id',
        'rental_property_id',
        'label',
        'rent_amount',
        'bedrooms',
        'unit_type',
        'description',
        'images',
        'status',
        'is_listed',
    ];

    protected $casts = [
        'rent_amount' => 'decimal:2',
        'images'      => 'array',
        'is_listed'   => 'boolean',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function property()
    {
        return $this->belongsTo(RentalProperty::class, 'rental_property_id');
    }

    public function tenant()
    {
        return $this->hasOne(RentalTenant::class)->where('status', 'active');
    }

    public function tenants()
    {
        return $this->hasMany(RentalTenant::class);
    }

    public function payments()
    {
        return $this->hasMany(RentalPayment::class);
    }

    public function isVacant(): bool
    {
        return $this->status === 'vacant';
    }

    /**
     * Location text for display + public search.
     */
    public function getLocationTextAttribute(): string
    {
        if ($this->property) {
            return collect([$this->property->name, $this->property->estate, $this->property->town])
                ->filter()->unique()->implode(', ');
        }
        return '';
    }

    /**
     * First stored image URL (or null).
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        $first = $this->images[0] ?? null;
        return $first ? asset('storage/' . $first) : null;
    }
}
