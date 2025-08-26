<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIBusinessRecommendation extends Model
{
    use HasFactory;

    protected $table = 'ai_business_recommendations';

    protected $fillable = [
        'business_id',
        'analysis_id',
        'category',
        'priority',
        'title',
        'description',
        'action_items',
        'expected_impact',
        'is_implemented',
        'implemented_at',
        'implementation_notes',
    ];

    protected $casts = [
        'action_items' => 'array',
        'is_implemented' => 'boolean',
        'implemented_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the business that owns the recommendation
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the analysis that generated this recommendation
     */
    public function analysis(): BelongsTo
    {
        return $this->belongsTo(AIBusinessAnalysis::class, 'analysis_id');
    }

    /**
     * Mark recommendation as implemented
     */
    public function markAsImplemented(string $notes = null): bool
    {
        return $this->update([
            'is_implemented' => true,
            'implemented_at' => now(),
            'implementation_notes' => $notes,
        ]);
    }

    /**
     * Mark recommendation as not implemented
     */
    public function markAsNotImplemented(): bool
    {
        return $this->update([
            'is_implemented' => false,
            'implemented_at' => null,
            'implementation_notes' => null,
        ]);
    }

    /**
     * Get priority level as numeric value for sorting
     */
    public function getPriorityNumericAttribute(): int
    {
        return match($this->priority) {
            'high' => 3,
            'medium' => 2,
            'low' => 1,
            default => 0,
        };
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    /**
     * Get category icon for UI
     */
    public function getCategoryIconAttribute(): string
    {
        return match($this->category) {
            'revenue_optimization', 'Revenue Growth' => 'fa-chart-line',
            'cost_management', 'Cost Control' => 'fa-dollar-sign',
            'operational_efficiency', 'Operations' => 'fa-cogs',
            'growth_opportunities' => 'fa-rocket',
            'risk_factors' => 'fa-exclamation-triangle',
            'market_expansion' => 'fa-globe',
            'technology_adoption' => 'fa-laptop',
            'staff_development' => 'fa-users',
            default => 'fa-lightbulb',
        };
    }

    /**
     * Get formatted action items as HTML list
     */
    public function getActionItemsHtmlAttribute(): string
    {
        if (empty($this->action_items)) {
            return '';
        }

        $items = collect($this->action_items)->map(function ($item) {
            return "<li>" . e($item) . "</li>";
        })->implode('');

        return "<ul>{$items}</ul>";
    }

    /**
     * Check if recommendation is recent (within last 30 days)
     */
    public function isRecent(): bool
    {
        return $this->created_at >= now()->subDays(30);
    }

    /**
     * Check if recommendation is overdue (created more than 90 days ago and not implemented)
     */
    public function isOverdue(): bool
    {
        return !$this->is_implemented && $this->created_at < now()->subDays(90);
    }

    /**
     * Scope for pending recommendations
     */
    public function scopePending($query)
    {
        return $query->where('is_implemented', false);
    }

    /**
     * Scope for implemented recommendations
     */
    public function scopeImplemented($query)
    {
        return $query->where('is_implemented', true);
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for high priority recommendations
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for recent recommendations
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for overdue recommendations
     */
    public function scopeOverdue($query)
    {
        return $query->where('is_implemented', false)
                    ->where('created_at', '<', now()->subDays(90));
    }
}
