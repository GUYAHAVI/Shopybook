<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIBusinessAnalysis extends Model
{
    use HasFactory;

    protected $table = 'ai_business_analysis';

    protected $fillable = [
        'business_id',
        'analysis_type',
        'analysis_data',
        'predicted_income',
        'current_income',
        'improvement_potential',
        'model_version',
        'confidence_score',
    ];

    protected $casts = [
        'analysis_data' => 'array',
        'predicted_income' => 'decimal:2',
        'current_income' => 'decimal:2',
        'improvement_potential' => 'decimal:2',
        'confidence_score' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the business that owns the analysis
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the recommendations for this analysis
     */
    public function recommendations(): HasMany
    {
        return $this->hasMany(AIBusinessRecommendation::class, 'analysis_id');
    }

    /**
     * Get high priority recommendations
     */
    public function highPriorityRecommendations()
    {
        return $this->recommendations()->where('priority', 'high');
    }

    /**
     * Get recommendations by category
     */
    public function getRecommendationsByCategory(string $category)
    {
        return $this->recommendations()->where('category', $category);
    }

    /**
     * Check if analysis has predictions
     */
    public function hasPredictions(): bool
    {
        return !is_null($this->predicted_income);
    }

    /**
     * Get improvement percentage
     */
    public function getImprovementPercentageAttribute(): float
    {
        if ($this->current_income <= 0) {
            return 0;
        }

        return (($this->improvement_potential / $this->current_income) * 100);
    }

    /**
     * Check if analysis shows growth potential
     */
    public function hasGrowthPotential(): bool
    {
        return $this->improvement_potential > 0;
    }

    /**
     * Get confidence level as text
     */
    public function getConfidenceLevelAttribute(): string
    {
        if (is_null($this->confidence_score)) {
            return 'unknown';
        }

        if ($this->confidence_score >= 0.8) {
            return 'high';
        } elseif ($this->confidence_score >= 0.6) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get analysis insights by category
     */
    public function getInsightsByCategory(string $category): array
    {
        $insights = $this->analysis_data['insights'] ?? [];
        return $insights[$category] ?? [];
    }

    /**
     * Get current performance metrics
     */
    public function getCurrentPerformance(): array
    {
        return $this->analysis_data['current_performance'] ?? [];
    }

    /**
     * Get benchmarks data
     */
    public function getBenchmarks(): array
    {
        return $this->analysis_data['benchmarks'] ?? [];
    }

    /**
     * Get predictions data
     */
    public function getPredictions(): array
    {
        return $this->analysis_data['predictions'] ?? [];
    }

    /**
     * Check if analysis is recent (within last 24 hours)
     */
    public function isRecent(): bool
    {
        return $this->created_at >= now()->subHours(24);
    }

    /**
     * Check if analysis needs refresh (older than 7 days)
     */
    public function needsRefresh(): bool
    {
        return $this->created_at < now()->subDays(7);
    }

    /**
     * Scope for recent analyses
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for high confidence analyses
     */
    public function scopeHighConfidence($query)
    {
        return $query->where('confidence_score', '>=', 0.8);
    }

    /**
     * Scope by analysis type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('analysis_type', $type);
    }
}
