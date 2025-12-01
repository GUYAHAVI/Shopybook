<?php

namespace Database\Seeders;

use App\Models\WebsiteTheme;
use Illuminate\Database\Seeder;

class WebsiteThemesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Modern Minimal',
                'slug' => 'modern-minimal',
                'description' => 'Clean, minimalist design with elegant typography and smooth animations',
                'category' => 'business',
                'style' => 'modern',
                'is_free' => true,
                'is_active' => true,
                'preview_image' => '/webbuilder/business-partners-shaking-hands-agreement.jpg',
                'thumbnail' => '/webbuilder/business-partners-shaking-hands-agreement.jpg',
                'default_colors' => [
                    'primary' => '#4F46E5',
                    'secondary' => '#06B6D4',
                    'accent' => '#F59E0B',
                    'background' => '#FFFFFF',
                    'text' => '#1F2937',
                    'light' => '#F3F4F6',
                    'dark' => '#111827',
                ],
                'default_fonts' => [
                    'heading' => 'Inter',
                    'body' => 'Inter',
                ],
                'available_sections' => ['hero', 'features', 'services', 'stats', 'testimonials', 'cta', 'about', 'team', 'contact'],
            ],
            [
                'name' => 'Bold & Creative',
                'slug' => 'bold-creative',
                'description' => 'Eye-catching design with vibrant colors and dynamic layouts',
                'category' => 'business',
                'style' => 'creative',
                'is_free' => true,
                'is_active' => true,
                'preview_image' => '/webbuilder/african-entrepreneur-start-up-company-reading-charts-documents-paperwork-diverse-team-business-people-analyzing-company-financial-reports-from-computer-successful-corporate-professional-en.jpg',
                'thumbnail' => '/webbuilder/african-entrepreneur-start-up-company-reading-charts-documents-paperwork-diverse-team-business-people-analyzing-company-financial-reports-from-computer-successful-corporate-professional-en.jpg',
                'default_colors' => [
                    'primary' => '#EC4899',
                    'secondary' => '#8B5CF6',
                    'accent' => '#F59E0B',
                    'background' => '#FFFFFF',
                    'text' => '#1F2937',
                    'light' => '#FDF4FF',
                    'dark' => '#831843',
                ],
                'default_fonts' => [
                    'heading' => 'Poppins',
                    'body' => 'Open Sans',
                ],
                'available_sections' => ['hero', 'features', 'portfolio', 'services', 'stats', 'testimonials', 'cta', 'team', 'gallery', 'contact'],
            ],
            [
                'name' => 'Classic Professional',
                'slug' => 'classic-professional',
                'description' => 'Traditional business design with timeless elegance',
                'category' => 'business',
                'style' => 'classic',
                'is_free' => true,
                'is_active' => true,
                'preview_image' => '/webbuilder/colleagues-reviewing-plans-tablet.jpg',
                'thumbnail' => '/webbuilder/colleagues-reviewing-plans-tablet.jpg',
                'default_colors' => [
                    'primary' => '#1E40AF',
                    'secondary' => '#059669',
                    'accent' => '#DC2626',
                    'background' => '#FFFFFF',
                    'text' => '#374151',
                    'light' => '#EFF6FF',
                    'dark' => '#1E3A8A',
                ],
                'default_fonts' => [
                    'heading' => 'Merriweather',
                    'body' => 'Lato',
                ],
                'available_sections' => ['hero', 'about', 'features', 'services', 'stats', 'testimonials', 'team', 'cta', 'contact'],
            ],
            [
                'name' => 'Dark Mode Pro',
                'slug' => 'dark-mode-pro',
                'description' => 'Sleek dark theme perfect for tech and creative businesses',
                'category' => 'business',
                'style' => 'modern',
                'is_free' => true,
                'is_active' => true,
                'preview_image' => '/webbuilder/young-businessmen-standing-together-holding-laptop-discussing-business.jpg',
                'thumbnail' => '/webbuilder/young-businessmen-standing-together-holding-laptop-discussing-business.jpg',
                'default_colors' => [
                    'primary' => '#8B5CF6',
                    'secondary' => '#06B6D4',
                    'accent' => '#F59E0B',
                    'background' => '#0F172A',
                    'text' => '#E2E8F0',
                    'light' => '#1E293B',
                    'dark' => '#020617',
                ],
                'default_fonts' => [
                    'heading' => 'Space Grotesk',
                    'body' => 'Inter',
                ],
                'available_sections' => ['hero', 'features', 'services', 'pricing', 'stats', 'testimonials', 'cta', 'portfolio', 'contact'],
            ],
            [
                'name' => 'Restaurant Deluxe',
                'slug' => 'restaurant-deluxe',
                'description' => 'Beautiful design for restaurants with menu showcase',
                'category' => 'restaurant',
                'style' => 'classic',
                'is_free' => true,
                'is_active' => true,
                'preview_image' => '/webbuilder/pexels-goumbik-669610.jpg',
                'thumbnail' => '/webbuilder/pexels-goumbik-669610.jpg',
                'default_colors' => [
                    'primary' => '#B91C1C',
                    'secondary' => '#92400E',
                    'accent' => '#F59E0B',
                    'background' => '#FFFBEB',
                    'text' => '#1F2937',
                    'light' => '#FEF3C7',
                    'dark' => '#78350F',
                ],
                'default_fonts' => [
                    'heading' => 'Playfair Display',
                    'body' => 'Lato',
                ],
                'available_sections' => ['hero', 'features', 'menu', 'chef-specials', 'gallery', 'testimonials', 'reservations', 'hours', 'contact'],
            ],
            [
                'name' => 'E-Commerce Fresh',
                'slug' => 'ecommerce-fresh',
                'description' => 'Modern online store design with product showcase',
                'category' => 'store',
                'style' => 'modern',
                'is_free' => true,
                'is_active' => true,
                'preview_image' => '/webbuilder/composition-beauty-industry-products-women.jpg',
                'thumbnail' => '/webbuilder/composition-beauty-industry-products-women.jpg',
                'default_colors' => [
                    'primary' => '#059669',
                    'secondary' => '#0891B2',
                    'accent' => '#F97316',
                    'background' => '#FFFFFF',
                    'text' => '#111827',
                    'light' => '#F0FDF4',
                    'dark' => '#064E3B',
                ],
                'default_fonts' => [
                    'heading' => 'Montserrat',
                    'body' => 'Nunito',
                ],
                'available_sections' => ['hero', 'features', 'categories', 'products', 'bestsellers', 'deals', 'testimonials', 'newsletter', 'contact'],
            ],
            [
                'name' => 'Portfolio Showcase',
                'slug' => 'portfolio-showcase',
                'description' => 'Perfect for creatives to showcase their work',
                'category' => 'portfolio',
                'style' => 'creative',
                'is_free' => true,
                'is_active' => true,
                'preview_image' => '/webbuilder/medium-shot-man-looking-jewelry.jpg',
                'thumbnail' => '/webbuilder/medium-shot-man-looking-jewelry.jpg',
                'default_colors' => [
                    'primary' => '#6366F1',
                    'secondary' => '#EC4899',
                    'accent' => '#10B981',
                    'background' => '#FFFFFF',
                    'text' => '#18181B',
                    'light' => '#F5F3FF',
                    'dark' => '#4338CA',
                ],
                'default_fonts' => [
                    'heading' => 'Raleway',
                    'body' => 'Work Sans',
                ],
                'available_sections' => ['hero', 'portfolio', 'skills', 'experience', 'testimonials', 'about', 'services', 'cta', 'contact'],
            ],
            [
                'name' => 'Service Provider',
                'slug' => 'service-provider',
                'description' => 'Professional theme for service-based businesses',
                'category' => 'service',
                'style' => 'professional',
                'is_free' => true,
                'is_active' => true,
                'preview_image' => '/webbuilder/happy-black-woman-holding-skincare-product.jpg',
                'thumbnail' => '/webbuilder/happy-black-woman-holding-skincare-product.jpg',
                'default_colors' => [
                    'primary' => '#0891B2',
                    'secondary' => '#7C3AED',
                    'accent' => '#F59E0B',
                    'background' => '#FFFFFF',
                    'text' => '#1F2937',
                    'light' => '#ECFEFF',
                    'dark' => '#164E63',
                ],
                'default_fonts' => [
                    'heading' => 'Roboto',
                    'body' => 'Source Sans Pro',
                ],
                'available_sections' => ['hero', 'features', 'services', 'process', 'pricing', 'testimonials', 'booking', 'faq', 'contact'],
            ],
            [
                'name' => 'Startup Launch',
                'slug' => 'startup-launch',
                'description' => 'Modern SaaS landing page design',
                'category' => 'business',
                'style' => 'modern',
                'is_free' => false,
                'is_active' => true,
                'price' => 49.99,
                'preview_image' => '/webbuilder/beautiful-three-welldressed-afro-american-girls-customers-with-colored-shopping-bags-mobile-phone-shop-choosing-smartphone.jpg',
                'thumbnail' => '/webbuilder/beautiful-three-welldressed-afro-american-girls-customers-with-colored-shopping-bags-mobile-phone-shop-choosing-smartphone.jpg',
                'default_colors' => [
                    'primary' => '#7C3AED',
                    'secondary' => '#06B6D4',
                    'accent' => '#F59E0B',
                    'background' => '#FFFFFF',
                    'text' => '#111827',
                    'light' => '#FAF5FF',
                    'dark' => '#581C87',
                ],
                'default_fonts' => [
                    'heading' => 'DM Sans',
                    'body' => 'Inter',
                ],
                'available_sections' => ['hero', 'features', 'benefits', 'how-it-works', 'pricing', 'testimonials', 'stats', 'faq', 'cta', 'contact'],
            ],
        ];

        foreach ($themes as $themeData) {
            WebsiteTheme::updateOrCreate(
                ['slug' => $themeData['slug']],
                $themeData
            );
        }

        $this->command->info('✅ Created ' . count($themes) . ' beautiful themes with preview images!');
    }
}

