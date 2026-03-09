<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIBusinessMemory extends Model
{
    protected $table = 'ai_business_memory';

    protected $fillable = [
        'business_id',
        'facet_key',
        'facet_value',
        'confidence',
        'source',
    ];

    /**
     * Return all memory facets for a business as a key => value array.
     */
    public static function forBusiness(string $businessId): array
    {
        return static::where('business_id', $businessId)
            ->orderBy('confidence', 'desc')
            ->get(['facet_key', 'facet_value'])
            ->pluck('facet_value', 'facet_key')
            ->all();
    }
}
