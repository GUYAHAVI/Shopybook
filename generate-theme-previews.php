<?php
/**
 * Generate Theme Preview Images
 * This script creates SVG-based preview images for each theme showing actual layout mockups
 */

$themes = [
    'modern-minimal' => [
        'name' => 'Modern Minimal',
        'primary' => '#4F46E5',
        'secondary' => '#06B6D4',
        'background' => '#FFFFFF',
        'text' => '#1F2937',
    ],
    'bold-creative' => [
        'name' => 'Bold & Creative',
        'primary' => '#EC4899',
        'secondary' => '#8B5CF6',
        'background' => '#FFFFFF',
        'text' => '#1F2937',
    ],
    'classic-professional' => [
        'name' => 'Classic Professional',
        'primary' => '#1E40AF',
        'secondary' => '#059669',
        'background' => '#FFFFFF',
        'text' => '#374151',
    ],
    'dark-mode-pro' => [
        'name' => 'Dark Mode Pro',
        'primary' => '#8B5CF6',
        'secondary' => '#06B6D4',
        'background' => '#0F172A',
        'text' => '#E2E8F0',
    ],
    'restaurant-deluxe' => [
        'name' => 'Restaurant Deluxe',
        'primary' => '#B91C1C',
        'secondary' => '#92400E',
        'background' => '#FFFBEB',
        'text' => '#1F2937',
    ],
    'ecommerce-fresh' => [
        'name' => 'E-Commerce Fresh',
        'primary' => '#059669',
        'secondary' => '#0891B2',
        'background' => '#FFFFFF',
        'text' => '#111827',
    ],
    'portfolio-showcase' => [
        'name' => 'Portfolio Showcase',
        'primary' => '#6366F1',
        'secondary' => '#EC4899',
        'background' => '#FFFFFF',
        'text' => '#18181B',
    ],
    'service-provider' => [
        'name' => 'Service Provider',
        'primary' => '#0891B2',
        'secondary' => '#7C3AED',
        'background' => '#FFFFFF',
        'text' => '#1F2937',
    ],
    'startup-launch' => [
        'name' => 'Startup Launch',
        'primary' => '#7C3AED',
        'secondary' => '#06B6D4',
        'background' => '#FFFFFF',
        'text' => '#111827',
    ],
];

function generateThemePreview($slug, $theme, $size = 'large') {
    $width = $size === 'large' ? 1200 : 400;
    $height = $size === 'large' ? 800 : 300;
    
    $svg = <<<SVG
<svg width="{$width}" height="{$height}" xmlns="http://www.w3.org/2000/svg">
    <!-- Background -->
    <rect width="{$width}" height="{$height}" fill="{$theme['background']}"/>
    
    <!-- Header -->
    <rect width="{$width}" height="80" fill="{$theme['primary']}" opacity="0.1"/>
    <rect x="40" y="25" width="120" height="30" rx="4" fill="{$theme['primary']}"/>
    <text x="100" y="47" font-family="Arial, sans-serif" font-size="16" fill="{$theme['background']}" text-anchor="middle" font-weight="bold">LOGO</text>
    
    <!-- Navigation -->
    <rect x="{($width-300)}" y="30" width="60" height="20" rx="3" fill="{$theme['text']}" opacity="0.2"/>
    <rect x="{($width-220)}" y="30" width="60" height="20" rx="3" fill="{$theme['text']}" opacity="0.2"/>
    <rect x="{($width-140)}" y="30" width="60" height="20" rx="3" fill="{$theme['secondary']}"/>
    
    <!-- Hero Section -->
    <rect x="40" y="120" width="{($width/2-60)}" height="40" rx="4" fill="{$theme['text']}" opacity="0.8"/>
    <rect x="40" y="175" width="{($width/2-100)}" height="20" rx="3" fill="{$theme['text']}" opacity="0.4"/>
    <rect x="40" y="205" width="{($width/2-120)}" height="20" rx="3" fill="{$theme['text']}" opacity="0.4"/>
    <rect x="40" y="250" width="140" height="45" rx="6" fill="{$theme['primary']}"/>
    <rect x="200" y="250" width="140" height="45" rx="6" fill="{$theme['secondary']}" opacity="0.3"/>
    
    <!-- Hero Image -->
    <rect x="{($width/2+20)}" y="120" width="{($width/2-60)}" height="{($height-220)}" rx="8" fill="{$theme['secondary']}" opacity="0.2"/>
    <circle cx="{($width/2+$width/4)}" cy="{($height/2)}" r="60" fill="{$theme['primary']}" opacity="0.3"/>
    
    <!-- Content Cards -->
    <rect x="40" y="{($height-180)}" width="{($width/3-60)}" height="140" rx="8" fill="{$theme['text']}" opacity="0.05"/>
    <rect x="{($width/3+10)}" y="{($height-180)}" width="{($width/3-60)}" height="140" rx="8" fill="{$theme['text']}" opacity="0.05"/>
    <rect x="{($width*2/3-20)}" y="{($height-180)}" width="{($width/3-60)}" height="140" rx="8" fill="{$theme['text']}" opacity="0.05"/>
    
    <!-- Card Icons -->
    <circle cx="110" cy="{($height-150)}" r="20" fill="{$theme['primary']}" opacity="0.2"/>
    <circle cx="{($width/2)}" cy="{($height-150)}" r="20" fill="{$theme['secondary']}" opacity="0.2"/>
    <circle cx="{($width-110)}" cy="{($height-150)}" r="20" fill="{$theme['primary']}" opacity="0.2"/>
    
    <!-- Theme Name Badge -->
    <rect x="{($width/2-150)}" y="{($height-40)}" width="300" height="30" rx="15" fill="{$theme['primary']}"/>
    <text x="{($width/2)}" y="{($height-20)}" font-family="Arial, sans-serif" font-size="14" fill="{$theme['background']}" text-anchor="middle" font-weight="bold">{$theme['name']}</text>
</svg>
SVG;

    return $svg;
}

// Generate previews for all themes
foreach ($themes as $slug => $theme) {
    // Large preview (1200x800)
    $largeSvg = generateThemePreview($slug, $theme, 'large');
    file_put_contents(__DIR__ . "/public/images/theme-previews/{$slug}-large.svg", $largeSvg);
    
    // Thumbnail (400x300)
    $thumbnailSvg = generateThemePreview($slug, $theme, 'thumbnail');
    file_put_contents(__DIR__ . "/public/images/theme-previews/{$slug}-thumbnail.svg", $thumbnailSvg);
    
    echo "✅ Generated previews for: {$theme['name']}\n";
}

echo "\n🎉 All theme preview images generated successfully!\n";
echo "📁 Location: public/images/theme-previews/\n";
