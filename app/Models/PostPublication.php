<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostPublication extends Model
{
    use HasFactory;

    protected $fillable = [
        'marketing_post_id',
        'social_media_account_id',
        'platform_post_id',
        'status',
        'platform_response',
        'error_message',
        'published_at',
        'engagement_metrics',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'engagement_metrics' => 'array',
    ];

    public function marketingPost(): BelongsTo
    {
        return $this->belongsTo(MarketingPost::class);
    }

    public function socialMediaAccount(): BelongsTo
    {
        return $this->belongsTo(SocialMediaAccount::class);
    }

    /**
     * Scope for successful publications
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for failed publications
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
