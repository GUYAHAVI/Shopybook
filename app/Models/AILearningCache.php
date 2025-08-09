<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AILearningCache extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'learned_data'
    ];

    protected $casts = [
        'learned_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the business that owns the learning cache
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get competitor insights from learned data
     */
    public function getCompetitorInsightsAttribute()
    {
        return $this->learned_data['competitor_insights'] ?? [];
    }

    /**
     * Get market trends from learned data
     */
    public function getMarketTrendsAttribute()
    {
        return $this->learned_data['market_trends'] ?? [];
    }

    /**
     * Get social insights from learned data
     */
    public function getSocialInsightsAttribute()
    {
        return $this->learned_data['social_insights'] ?? [];
    }

    /**
     * Get learning timestamp
     */
    public function getLearnedAtAttribute()
    {
        return $this->learned_data['learned_at'] ?? null;
    }
}
