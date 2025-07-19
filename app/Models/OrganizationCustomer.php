<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationCustomer extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'email',
        'phone',
        'kra_pin',
        'address',
        'city',
        'country',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    // Future: public function orders() { ... }
} 