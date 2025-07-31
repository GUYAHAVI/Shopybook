<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'title',
        'content',
        'media_files',
        'hashtags',
        'target_platforms',
        'post_type',
        'scheduled_at',
        'status',
        'ai_suggestions',
        'engagement_data',
    ];

    protected $casts = [
        'media_files' => 'array',
        'hashtags' => 'array',
        'target_platforms' => 'array',
        'scheduled_at' => 'datetime',
        'ai_suggestions' => 'array',
        'engagement_data' => 'array',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(PostPublication::class);
    }

    /**
     * Scope for scheduled posts
     */
    public function scopeScheduled($query)
    {
        return $query->where('post_type', 'scheduled');
    }

    /**
     * Scope for pending posts
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if post is ready to publish
     */
    public function isReadyToPublish(): bool
    {
        if ($this->post_type === 'immediate') {
            return $this->status === 'pending';
        }

        return $this->status === 'pending' && 
               $this->scheduled_at && 
               now()->greaterThanOrEqualTo($this->scheduled_at);
    }

    /**
     * Get status color for UI display
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'published' => 'success',
            'pending' => 'warning',
            'scheduled' => 'info',
            'failed' => 'danger',
            'partially_published' => 'warning',
            'draft' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get total engagement across all platforms
     */
    public function getTotalEngagementAttribute(): array
    {
        $total = [
            'likes' => 0,
            'shares' => 0,
            'comments' => 0,
            'views' => 0,
        ];

        foreach ($this->publications as $publication) {
            if ($publication->engagement_metrics) {
                foreach ($total as $key => $value) {
                    $total[$key] += $publication->engagement_metrics[$key] ?? 0;
                }
            }
        }

        return $total;
    }
}
