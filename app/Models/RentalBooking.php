<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalBooking extends Model
{
    protected $fillable = [
        'business_id', 'rental_unit_id', 'name', 'phone', 'email',
        'preferred_move_in', 'message', 'status',
    ];

    protected $casts = [
        'preferred_move_in' => 'date',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function unit()
    {
        return $this->belongsTo(RentalUnit::class, 'rental_unit_id');
    }
}
