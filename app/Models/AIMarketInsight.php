<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIMarketInsight extends Model
{
    protected $table = 'ai_market_insights';

    protected $fillable = [
        'category',
        'title',
        'summary',
        'source_url',
        'source_name',
        'keywords',
        'relevance_score',
        'published_at',
    ];

    protected $casts = [
        'keywords'     => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Return the most recent, highest-relevance insights for a category.
     */
    public static function forCategory(string $category, int $limit = 5): array
    {
        return static::where('category', $category)
            ->where('published_at', '>=', now()->subDays(60))
            ->orderByDesc('relevance_score')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get(['title', 'summary', 'source_name', 'published_at'])
            ->map(fn ($row) => [
                'title'       => $row->title,
                'summary'     => $row->summary,
                'source'      => $row->source_name,
                'published'   => $row->published_at?->format('M d, Y'),
            ])
            ->all();
    }
}
