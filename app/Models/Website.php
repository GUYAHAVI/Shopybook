<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'subdomain',
        'is_active',
        'is_published',
        'theme_id',
        'colors',
        'fonts',
        'settings',
        'seo_settings',
        'favicon_path',
        'logo_path',
        'custom_domain',
        'ssl_enabled',
        'total_views',
        'total_visits',
        'last_published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'colors' => 'array',
        'fonts' => 'array',
        'settings' => 'array',
        'seo_settings' => 'array',
        'ssl_enabled' => 'boolean',
        'last_published_at' => 'datetime',
    ];

    /**
     * Get the business that owns the website
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the theme
     */
    public function theme()
    {
        return $this->belongsTo(WebsiteTheme::class);
    }

    /**
     * Get all pages
     */
    public function pages()
    {
        return $this->hasMany(WebsitePage::class);
    }

    /**
     * Get published pages
     */
    public function publishedPages()
    {
        return $this->hasMany(WebsitePage::class)->where('is_published', true)->orderBy('order');
    }

    /**
     * Get the homepage
     */
    public function homepage()
    {
        return $this->hasOne(WebsitePage::class)->where('is_homepage', true);
    }

    /**
     * Get menu pages
     */
    public function menuPages()
    {
        return $this->hasMany(WebsitePage::class)
            ->where('is_published', true)
            ->where('show_in_menu', true)
            ->orderBy('order');
    }

    /**
     * Get website URL (environment-aware)
     */
    public function getUrlAttribute(): string
    {
        // Custom domain takes precedence
        if ($this->custom_domain) {
            $protocol = $this->ssl_enabled ? 'https' : 'http';
            return "{$protocol}://{$this->custom_domain}";
        }
        
        // Check if we're in local development
        if ($this->isLocalEnvironment()) {
            return $this->getLocalUrl();
        }
        
        // Production subdomain URL
        return "https://{$this->subdomain}.shopybook.com";
    }
    
    /**
     * Check if running in local environment
     */
    protected function isLocalEnvironment(): bool
    {
        $appUrl = config('app.url', '');
        $appEnv = config('app.env', 'production');
        
        return $appEnv === 'local' || 
               str_contains($appUrl, 'localhost') || 
               str_contains($appUrl, '127.0.0.1') ||
               str_contains($appUrl, '::1');
    }
    
    /**
     * Get local development URL
     */
    protected function getLocalUrl(): string
    {
        $baseUrl = config('app.url', 'http://localhost');
        
        // If using .localhost subdomain style
        if (str_contains($baseUrl, '.localhost')) {
            return "http://{$this->subdomain}.localhost:8000";
        }
        
        // Default path-based for 127.0.0.1 or localhost
        $baseUrl = rtrim($baseUrl, '/');
        return "{$baseUrl}/website/{$this->subdomain}";
    }

    /**
     * Get full URL with path
     */
    public function getFullUrl(?string $path = null): string
    {
        $url = $this->url;
        return $path ? "{$url}/{$path}" : $url;
    }

    /**
     * Get color scheme (theme + overrides)
     */
    public function getColorScheme(): array
    {
        $themeColors = $this->theme?->getDefaultColorScheme() ?? [];
        $customColors = $this->colors ?? [];
        
        return array_merge($themeColors, $customColors);
    }

    /**
     * Get fonts (theme + overrides)
     */
    public function getFonts(): array
    {
        $themeFonts = $this->theme?->getDefaultFonts() ?? [];
        $customFonts = $this->fonts ?? [];
        
        return array_merge($themeFonts, $customFonts);
    }

    /**
     * Get settings with defaults
     */
    public function getSettings(): array
    {
        return array_merge([
            'site_name' => $this->business->name,
            'tagline' => $this->business->description,
            'logo' => $this->logo_path ?? $this->business->logo_path,
            'favicon' => $this->favicon_path,
            'footer_text' => "© " . date('Y') . " {$this->business->name}. All rights reserved.",
            'contact_email' => $this->business->email,
            'contact_phone' => $this->business->phone,
            'show_footer' => true,
            'show_social_links' => true,
        ], $this->settings ?? []);
    }

    /**
     * Get SEO settings with defaults
     */
    public function getSeoSettings(): array
    {
        return array_merge([
            'meta_title' => $this->business->name,
            'meta_description' => $this->business->description,
            'meta_keywords' => '',
            'og_image' => $this->business->cover_path,
            'google_analytics_id' => null,
            'facebook_pixel_id' => null,
        ], $this->seo_settings ?? []);
    }

    /**
     * Publish the website
     */
    public function publish(): bool
    {
        $this->update([
            'is_published' => true,
            'last_published_at' => now(),
        ]);

        return true;
    }

    /**
     * Unpublish the website
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
        $this->increment('total_views');
    }

    /**
     * Increment visits
     */
    public function incrementVisits(): void
    {
        $this->increment('total_visits');
    }

    /**
     * Check if website is accessible
     */
    public function isAccessible(): bool
    {
        return $this->is_active && $this->is_published;
    }

    /**
     * Scope active websites
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope published websites
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}

