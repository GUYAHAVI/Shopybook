<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalProperty extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'town',
        'estate',
        'description',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function units()
    {
        return $this->hasMany(RentalUnit::class);
    }

    public function getLocationAttribute(): string
    {
        return collect([$this->estate, $this->town])->filter()->implode(', ');
    }
}
