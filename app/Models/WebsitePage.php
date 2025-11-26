<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebsitePage extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id',
        'title',
        'slug',
        'description',
        'is_homepage',
        'is_published',
        'show_in_menu',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'layout',
        'page_settings',
        'order',
        'views',
    ];

    protected $casts = [
        'is_homepage' => 'boolean',
        'is_published' => 'boolean',
        'show_in_menu' => 'boolean',
        'page_settings' => 'array',
    ];

    /**
     * Get the website
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * Get all sections
     */
    public function sections()
    {
        return $this->hasMany(WebsiteSection::class, 'page_id')->orderBy('order');
    }

    /**
     * Get visible sections
     */
    public function visibleSections()
    {
        return $this->hasMany(WebsiteSection::class, 'page_id')
            ->where('is_visible', true)
            ->orderBy('order');
    }

    /**
     * Get page URL
     */
    public function getUrlAttribute(): string
    {
        if ($this->is_homepage) {
            return $this->website->url;
        }
        
        return $this->website->getFullUrl($this->slug);
    }

    /**
     * Get full URL
     */
    public function getFullUrl(): string
    {
        return $this->url;
    }

    /**
     * Get meta title (with fallback)
     */
    public function getMetaTitle(): string
    {
        return $this->meta_title ?? $this->title ?? $this->website->business->name;
    }

    /**
     * Get meta description (with fallback)
     */
    public function getMetaDescription(): string
    {
        return $this->meta_description ?? $this->description ?? $this->website->business->description ?? '';
    }

    /**
     * Get OG image (with fallback)
     */
    public function getOgImage(): ?string
    {
        return $this->og_image ?? $this->website->business->cover_path;
    }

    /**
     * Publish the page
     */
    public function publish(): bool
    {
        $this->update(['is_published' => true]);
        return true;
    }

    /**
     * Unpublish the page
     */
    public function unpublish(): bool
    {
        $this->update(['is_published' => false]);
        return true;
    }

    /**
     * Increment views
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Duplicate page
     */
    public function duplicate(): self
    {
        $newPage = $this->replicate();
        $newPage->title = $this->title . ' (Copy)';
        $newPage->slug = $this->slug . '-copy-' . Str::random(4);
        $newPage->is_homepage = false;
        $newPage->is_published = false;
        $newPage->views = 0;
        $newPage->save();

        // Duplicate sections
        foreach ($this->sections as $section) {
            $newSection = $section->replicate();
            $newSection->page_id = $newPage->id;
            $newSection->save();
        }

        return $newPage;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            // Auto-generate slug if not provided
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }

            // Ensure slug is unique within website
            $originalSlug = $page->slug;
            $counter = 1;
            while (static::where('website_id', $page->website_id)
                        ->where('slug', $page->slug)
                        ->exists()) {
                $page->slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Set order if not provided
            if ($page->order === 0 || $page->order === null) {
                $maxOrder = static::where('website_id', $page->website_id)->max('order') ?? 0;
                $page->order = $maxOrder + 1;
            }
        });

        static::updating(function ($page) {
            // Only one homepage per website
            if ($page->is_homepage && $page->isDirty('is_homepage')) {
                static::where('website_id', $page->website_id)
                    ->where('id', '!=', $page->id)
                    ->update(['is_homepage' => false]);
            }
        });
    }

    /**
     * Scope published pages
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope menu pages
     */
    public function scopeMenu($query)
    {
        return $query->where('show_in_menu', true)->orderBy('order');
    }
}

