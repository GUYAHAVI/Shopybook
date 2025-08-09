<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AILearningSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'automated_learning_enabled',
        'competitor_analysis_enabled',
        'market_trends_enabled',
        'social_media_learning_enabled',
        'learning_keywords',
        'excluded_competitors'
    ];

    protected $casts = [
        'automated_learning_enabled' => 'boolean',
        'competitor_analysis_enabled' => 'boolean',
        'market_trends_enabled' => 'boolean',
        'social_media_learning_enabled' => 'boolean',
        'learning_keywords' => 'array',
        'excluded_competitors' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the business that owns the learning settings
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Check if learning is enabled
     */
    public function isLearningEnabled()
    {
        return $this->automated_learning_enabled;
    }

    /**
     * Check if competitor analysis is enabled
     */
    public function isCompetitorAnalysisEnabled()
    {
        return $this->competitor_analysis_enabled;
    }

    /**
     * Check if market trends learning is enabled
     */
    public function isMarketTrendsEnabled()
    {
        return $this->market_trends_enabled;
    }

    /**
     * Check if social media learning is enabled
     */
    public function isSocialMediaLearningEnabled()
    {
        return $this->social_media_learning_enabled;
    }

    /**
     * Get active learning features
     */
    public function getActiveFeaturesAttribute()
    {
        $features = [];
        
        if ($this->competitor_analysis_enabled) {
            $features[] = 'Competitor Analysis';
        }
        
        if ($this->market_trends_enabled) {
            $features[] = 'Market Trends';
        }
        
        if ($this->social_media_learning_enabled) {
            $features[] = 'Social Media Learning';
        }
        
        return $features;
    }
}
