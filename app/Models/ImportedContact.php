<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportedContact extends Model
{
    protected $fillable = [
        'business_id',
        'contact_group_id',
        'name',
        'email',
        'phone',
        'company',
        'position',
        'address',
        'notes',
        'source',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function contactGroup(): BelongsTo
    {
        return $this->belongsTo(ContactGroup::class);
    }

    /**
     * Format phone number to international format if needed
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone;
        
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If it starts with 0, replace with 254 (Kenya)
        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }
        
        // If it doesn't start with country code, add 254
        if (strlen($phone) === 9) {
            $phone = '254' . $phone;
        }
        
        return $phone;
    }

    /**
     * Boot method to update contact count on parent group
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($contact) {
            $contact->contactGroup->updateContactCount();
        });

        static::deleted(function ($contact) {
            $contact->contactGroup->updateContactCount();
        });
    }
}




