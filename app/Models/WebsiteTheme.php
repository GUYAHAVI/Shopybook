<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteTheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'preview_url',
        'default_colors',
        'default_fonts',
        'available_sections',
        'default_layout',
        'category',
        'style',
        'is_free',
        'price',
        'is_active',
        'usage_count',
        'rating',
    ];

    protected $casts = [
        'default_colors' => 'array',
        'default_fonts' => 'array',
        'available_sections' => 'array',
        'default_layout' => 'array',
        'is_free' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    /**
     * Get websites using this theme
     */
    public function websites()
    {
        return $this->hasMany(Website::class, 'theme_id');
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    /**
     * Get default color scheme
     */
    public function getDefaultColorScheme(): array
    {
        return $this->default_colors ?? [
            'primary' => '#4F46E5',
            'secondary' => '#10B981',
            'accent' => '#F59E0B',
            'background' => '#FFFFFF',
            'text' => '#1F2937',
        ];
    }

    /**
     * Get default fonts
     */
    public function getDefaultFonts(): array
    {
        return $this->default_fonts ?? [
            'heading' => 'Poppins',
            'body' => 'Inter',
        ];
    }

    /**
     * Check if theme supports a section type
     */
    public function supportsSection(string $sectionType): bool
    {
        $availableSections = $this->available_sections ?? [];
        return empty($availableSections) || in_array($sectionType, $availableSections);
    }

    /**
     * Scope to get active themes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get free themes
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope by category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}

