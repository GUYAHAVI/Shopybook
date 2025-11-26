<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactGroup extends Model
{
    protected $fillable = [
        'business_id',
        'name',
        'description',
        'type',
        'contact_count',
    ];

    protected $casts = [
        'contact_count' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ImportedContact::class);
    }

    /**
     * Update the contact count for this group
     */
    public function updateContactCount(): void
    {
        $this->update([
            'contact_count' => $this->contacts()->count()
        ]);
    }

    /**
     * Get all phone numbers in this group
     */
    public function getPhoneNumbers(): array
    {
        return $this->contacts()
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->pluck('phone')
            ->toArray();
    }

    /**
     * Get all emails in this group
     */
    public function getEmails(): array
    {
        return $this->contacts()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->pluck('email')
            ->toArray();
    }
}



