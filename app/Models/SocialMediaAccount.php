<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialMediaAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'platform',
        'platform_user_id',
        'username',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'platform_data',
        'is_active',
        'last_connected_at',
    ];

    protected $casts = [
        'platform_data' => 'array',
        'is_active' => 'boolean',
        'token_expires_at' => 'datetime',
        'last_connected_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function postPublications(): HasMany
    {
        return $this->hasMany(PostPublication::class);
    }

    /**
     * Check if the token is expired
     */
    public function isTokenExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }

    /**
     * Get platform icon for UI
     */
    public function getPlatformIconAttribute(): string
    {
        $icons = [
            'facebook' => 'facebook',
            'instagram' => 'instagram',
            'twitter' => 'twitter',
            'linkedin' => 'linkedin',
            'tiktok' => 'tiktok',
            'youtube' => 'youtube',
            'pinterest' => 'pinterest',
            'snapchat' => 'snapchat',
            'whatsapp' => 'whatsapp',
            'telegram' => 'telegram',
            'discord' => 'discord',
            'reddit' => 'reddit',
        ];

        return $icons[$this->getAttribute('platform')] ?? 'share-alt';
    }

    /**
     * Get platform color for UI
     */
    public function getPlatformColorAttribute(): string
    {
        $colors = [
            'facebook' => 'primary',
            'instagram' => 'danger',
            'twitter' => 'info',
            'linkedin' => 'primary',
            'tiktok' => 'dark',
            'youtube' => 'danger',
            'pinterest' => 'danger',
            'snapchat' => 'warning',
            'whatsapp' => 'success',
            'telegram' => 'info',
            'discord' => 'primary',
            'reddit' => 'warning',
        ];

        return $colors[$this->getAttribute('platform')] ?? 'secondary';
    }

    /**
     * Get platform-specific posting limits
     */
    public function getPostingLimitsAttribute(): array
    {
        $limits = [
            'facebook' => ['posts_per_day' => 25, 'text_limit' => 63206, 'media_limit' => 10],
            'instagram' => ['posts_per_day' => 25, 'text_limit' => 2200, 'media_limit' => 10],
            'twitter' => ['posts_per_day' => 300, 'text_limit' => 280, 'media_limit' => 4],
            'linkedin' => ['posts_per_day' => 25, 'text_limit' => 3000, 'media_limit' => 9],
            'tiktok' => ['posts_per_day' => 3, 'text_limit' => 2200, 'media_limit' => 1],
            'youtube' => ['posts_per_day' => 6, 'text_limit' => 5000, 'media_limit' => 1],
            'pinterest' => ['posts_per_day' => 25, 'text_limit' => 500, 'media_limit' => 1],
            'snapchat' => ['posts_per_day' => 10, 'text_limit' => 250, 'media_limit' => 1],
            'whatsapp' => ['posts_per_day' => 1000, 'text_limit' => 4096, 'media_limit' => 1],
            'telegram' => ['posts_per_day' => 30, 'text_limit' => 4096, 'media_limit' => 10],
            'discord' => ['posts_per_day' => 50, 'text_limit' => 2000, 'media_limit' => 10],
            'reddit' => ['posts_per_day' => 5, 'text_limit' => 40000, 'media_limit' => 20],
        ];

        return $limits[$this->getAttribute('platform')] ?? ['posts_per_day' => 10, 'text_limit' => 1000, 'media_limit' => 5];
    }

    /**
     * Check if platform supports video content
     */
    public function supportsVideo(): bool
    {
        $videoSupported = [
            'facebook', 'instagram', 'twitter', 'linkedin', 'tiktok', 
            'youtube', 'pinterest', 'snapchat', 'telegram', 'discord'
        ];

        return in_array($this->getAttribute('platform'), $videoSupported);
    }

    /**
     * Check if platform supports live streaming
     */
    public function supportsLive(): bool
    {
        $liveSupported = [
            'facebook', 'instagram', 'twitter', 'linkedin', 'youtube', 'tiktok'
        ];

        return in_array($this->getAttribute('platform'), $liveSupported);
    }

    /**
     * Get platform-specific hashtag format
     */
    public function formatHashtags(array $hashtags): string
    {
        switch ($this->getAttribute('platform')) {
            case 'linkedin':
                // LinkedIn prefers hashtags at the end
                return ' ' . implode(' ', $hashtags);
            case 'twitter':
                // Twitter integrates hashtags in text
                return ' ' . implode(' ', $hashtags);
            case 'instagram':
                // Instagram allows many hashtags
                return "\n\n" . implode(' ', $hashtags);
            case 'tiktok':
                // TikTok uses hashtags for discovery
                return "\n\n" . implode(' ', $hashtags);
            default:
                return ' ' . implode(' ', array_slice($hashtags, 0, 5)); // Limit to 5 for other platforms
        }
    }
}
