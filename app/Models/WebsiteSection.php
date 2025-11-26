<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'type',
        'component_name',
        'content',
        'settings',
        'background_type',
        'background_value',
        'style_overrides',
        'is_visible',
        'show_on_mobile',
        'order',
        'animation',
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'style_overrides' => 'array',
        'is_visible' => 'boolean',
        'show_on_mobile' => 'boolean',
    ];

    /**
     * Get the page
     */
    public function page()
    {
        return $this->belongsTo(WebsitePage::class, 'page_id');
    }

    /**
     * Get content with defaults based on type
     */
    public function getContentWithDefaults(): array
    {
        $defaults = $this->getDefaultContentForType($this->type);
        return array_merge($defaults, $this->content ?? []);
    }

    /**
     * Get default content structure for section type
     */
    protected function getDefaultContentForType(string $type): array
    {
        return match($type) {
            'hero' => [
                'heading' => 'Welcome to Our Website',
                'subheading' => 'Your success is our priority',
                'cta_text' => 'Get Started',
                'cta_link' => '#contact',
                'image' => null,
            ],
            'about' => [
                'heading' => 'About Us',
                'text' => 'Tell your story here...',
                'image' => null,
            ],
            'features' => [
                'heading' => 'Our Features',
                'subheading' => 'What makes us special',
                'features' => [],
            ],
            'services' => [
                'heading' => 'Our Services',
                'subheading' => 'What we offer',
                'services' => [],
            ],
            'products' => [
                'heading' => 'Our Products',
                'subheading' => 'Featured products',
                'show_price' => true,
                'products_per_row' => 3,
                'max_products' => 6,
            ],
            'gallery' => [
                'heading' => 'Gallery',
                'images' => [],
                'columns' => 3,
            ],
            'testimonials' => [
                'heading' => 'What Our Clients Say',
                'testimonials' => [],
            ],
            'team' => [
                'heading' => 'Meet Our Team',
                'members' => [],
            ],
            'contact' => [
                'heading' => 'Get In Touch',
                'show_form' => true,
                'show_map' => false,
                'email' => null,
                'phone' => null,
                'address' => null,
            ],
            'cta' => [
                'heading' => 'Ready to get started?',
                'text' => 'Contact us today',
                'button_text' => 'Contact Us',
                'button_link' => '#contact',
            ],
            default => [],
        };
    }

    /**
     * Get settings with defaults
     */
    public function getSettingsWithDefaults(): array
    {
        return array_merge([
            'padding_top' => 'medium',
            'padding_bottom' => 'medium',
            'container_width' => 'default',
            'text_align' => 'left',
        ], $this->settings ?? []);
    }

    /**
     * Get background style
     */
    public function getBackgroundStyle(): string
    {
        if (!$this->background_value) {
            return '';
        }

        return match($this->background_type) {
            'color' => "background-color: {$this->background_value};",
            'image' => "background-image: url('{$this->background_value}'); background-size: cover; background-position: center;",
            'gradient' => "background: {$this->background_value};",
            default => '',
        };
    }

    /**
     * Duplicate section
     */
    public function duplicate(): self
    {
        $newSection = $this->replicate();
        $newSection->save();
        return $newSection;
    }

    /**
     * Move up in order
     */
    public function moveUp(): bool
    {
        $previousSection = static::where('page_id', $this->page_id)
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousSection) {
            $tempOrder = $this->order;
            $this->update(['order' => $previousSection->order]);
            $previousSection->update(['order' => $tempOrder]);
            return true;
        }

        return false;
    }

    /**
     * Move down in order
     */
    public function moveDown(): bool
    {
        $nextSection = static::where('page_id', $this->page_id)
            ->where('order', '>', $this->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextSection) {
            $tempOrder = $this->order;
            $this->update(['order' => $nextSection->order]);
            $nextSection->update(['order' => $tempOrder]);
            return true;
        }

        return false;
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($section) {
            // Set order if not provided
            if ($section->order === 0 || $section->order === null) {
                $maxOrder = static::where('page_id', $section->page_id)->max('order') ?? 0;
                $section->order = $maxOrder + 1;
            }
        });
    }

    /**
     * Scope visible sections
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope by type
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }
}

