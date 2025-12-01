<?php

namespace App\Services;

use App\Models\Website;
use App\Models\WebsitePage;
use App\Models\WebsiteSection;
use Database\Seeders\DefaultWebsiteContentSeeder;

class WebsiteContentService
{
    /**
     * Initialize a website with default pages and content
     */
    public function initializeWebsiteContent(Website $website): void
    {
        $category = $website->theme->category ?? 'business';
        $defaultContent = DefaultWebsiteContentSeeder::getDefaultContent($category);
        
        // Create Homepage
        $this->createHomepage($website, $defaultContent['homepage'] ?? []);
        
        // Create About Page
        if (isset($defaultContent['about'])) {
            $this->createAboutPage($website, $defaultContent['about']);
        }
        
        // Create Services Page
        if (isset($defaultContent['services'])) {
            $this->createServicesPage($website, $defaultContent['services']);
        }
        
        // Create Contact Page
        if (isset($defaultContent['contact'])) {
            $this->createContactPage($website, $defaultContent['contact']);
        }
        
        // Category-specific pages
        if ($category === 'restaurant' && isset($defaultContent['menu'])) {
            $this->createMenuPage($website, $defaultContent['menu']);
        }
        
        if ($category === 'portfolio' && isset($defaultContent['portfolio'])) {
            $this->createPortfolioPage($website, $defaultContent['portfolio']);
        }
    }
    
    /**
     * Create homepage with sections
     */
    protected function createHomepage(Website $website, array $content): WebsitePage
    {
        $page = WebsitePage::create([
            'website_id' => $website->id,
            'title' => 'Home',
            'slug' => 'home',
            'description' => 'Welcome to our website',
            'is_homepage' => true,
            'is_published' => true,
            'show_in_menu' => true,
            'meta_title' => $website->business->name . ' - Home',
            'meta_description' => 'Welcome to ' . $website->business->name,
            'order' => 1,
        ]);
        
        $order = 1;
        
        // Hero Section
        if (isset($content['hero'])) {
            $this->createSection($page, 'hero', $content['hero'], $order++);
        }
        
        // Features Section
        if (isset($content['features'])) {
            $this->createSection($page, 'features', $content['features'], $order++);
        }
        
        // Services Section
        if (isset($content['services'])) {
            $this->createSection($page, 'services', $content['services'], $order++);
        }
        
        // Stats Section
        if (isset($content['stats'])) {
            $this->createSection($page, 'stats', $content['stats'], $order++);
        }
        
        // Testimonials Section
        if (isset($content['testimonials'])) {
            $this->createSection($page, 'testimonials', $content['testimonials'], $order++);
        }
        
        // CTA Section
        if (isset($content['cta'])) {
            $this->createSection($page, 'cta', $content['cta'], $order++);
        }
        
        // Menu Preview (Restaurant)
        if (isset($content['menu_preview'])) {
            $this->createSection($page, 'menu-preview', $content['menu_preview'], $order++);
        }
        
        // Categories (Store)
        if (isset($content['categories'])) {
            $this->createSection($page, 'categories', $content['categories'], $order++);
        }
        
        // Portfolio Preview
        if (isset($content['portfolio'])) {
            $this->createSection($page, 'portfolio-preview', $content['portfolio'], $order++);
        }
        
        return $page;
    }
    
    /**
     * Create about page
     */
    protected function createAboutPage(Website $website, array $content): WebsitePage
    {
        $page = WebsitePage::create([
            'website_id' => $website->id,
            'title' => 'About Us',
            'slug' => 'about',
            'description' => 'Learn more about our company',
            'is_homepage' => false,
            'is_published' => true,
            'show_in_menu' => true,
            'meta_title' => 'About ' . $website->business->name,
            'meta_description' => 'Learn more about ' . $website->business->name,
            'order' => 2,
        ]);
        
        $order = 1;
        
        if (isset($content['hero'])) {
            $this->createSection($page, 'hero', $content['hero'], $order++);
        }
        
        if (isset($content['story'])) {
            $this->createSection($page, 'text-with-image', $content['story'], $order++);
        }
        
        if (isset($content['mission'])) {
            $this->createSection($page, 'text-block', $content['mission'], $order++);
        }
        
        if (isset($content['vision'])) {
            $this->createSection($page, 'text-block', $content['vision'], $order++);
        }
        
        if (isset($content['values'])) {
            $this->createSection($page, 'features', $content['values'], $order++);
        }
        
        if (isset($content['team'])) {
            $this->createSection($page, 'team', $content['team'], $order++);
        }
        
        return $page;
    }
    
    /**
     * Create services page
     */
    protected function createServicesPage(Website $website, array $content): WebsitePage
    {
        $page = WebsitePage::create([
            'website_id' => $website->id,
            'title' => 'Services',
            'slug' => 'services',
            'description' => 'Our services and offerings',
            'is_homepage' => false,
            'is_published' => true,
            'show_in_menu' => true,
            'meta_title' => 'Services - ' . $website->business->name,
            'meta_description' => 'Explore our services at ' . $website->business->name,
            'order' => 3,
        ]);
        
        $order = 1;
        
        if (isset($content['hero'])) {
            $this->createSection($page, 'hero', $content['hero'], $order++);
        }
        
        if (isset($content['services_list'])) {
            foreach ($content['services_list'] as $service) {
                $this->createSection($page, 'service-detail', $service, $order++);
            }
        }
        
        return $page;
    }
    
    /**
     * Create contact page
     */
    protected function createContactPage(Website $website, array $content): WebsitePage
    {
        $page = WebsitePage::create([
            'website_id' => $website->id,
            'title' => 'Contact Us',
            'slug' => 'contact',
            'description' => 'Get in touch with us',
            'is_homepage' => false,
            'is_published' => true,
            'show_in_menu' => true,
            'meta_title' => 'Contact - ' . $website->business->name,
            'meta_description' => 'Contact ' . $website->business->name,
            'order' => 4,
        ]);
        
        $order = 1;
        
        if (isset($content['hero'])) {
            $this->createSection($page, 'hero', $content['hero'], $order++);
        }
        
        // Contact form
        $this->createSection($page, 'contact-form', [
            'heading' => 'Send us a message',
            'subheading' => 'We\'ll get back to you within 24 hours',
        ], $order++);
        
        // Contact info
        if (isset($content['info'])) {
            $this->createSection($page, 'contact-info', $content['info'], $order++);
        }
        
        // Map
        $this->createSection($page, 'map', [
            'heading' => 'Find Us',
            'address' => $content['info']['address'] ?? '',
        ], $order++);
        
        return $page;
    }
    
    /**
     * Create menu page (for restaurants)
     */
    protected function createMenuPage(Website $website, array $content): WebsitePage
    {
        $page = WebsitePage::create([
            'website_id' => $website->id,
            'title' => 'Menu',
            'slug' => 'menu',
            'description' => 'Our menu and offerings',
            'is_homepage' => false,
            'is_published' => true,
            'show_in_menu' => true,
            'meta_title' => 'Menu - ' . $website->business->name,
            'meta_description' => 'View our menu at ' . $website->business->name,
            'order' => 2,
        ]);
        
        $order = 1;
        
        $this->createSection($page, 'hero', [
            'heading' => 'Our Menu',
            'subheading' => 'Crafted with passion, served with love',
        ], $order++);
        
        if (isset($content['categories'])) {
            foreach ($content['categories'] as $category) {
                $this->createSection($page, 'menu-category', $category, $order++);
            }
        }
        
        return $page;
    }
    
    /**
     * Create portfolio page
     */
    protected function createPortfolioPage(Website $website, array $content): WebsitePage
    {
        $page = WebsitePage::create([
            'website_id' => $website->id,
            'title' => 'Portfolio',
            'slug' => 'portfolio',
            'description' => 'View our work',
            'is_homepage' => false,
            'is_published' => true,
            'show_in_menu' => true,
            'meta_title' => 'Portfolio - ' . $website->business->name,
            'meta_description' => 'View our portfolio at ' . $website->business->name,
            'order' => 2,
        ]);
        
        $order = 1;
        
        $this->createSection($page, 'hero', [
            'heading' => 'Our Work',
            'subheading' => 'Projects we\'re proud of',
        ], $order++);
        
        $this->createSection($page, 'portfolio-grid', $content, $order++);
        
        return $page;
    }
    
    /**
     * Create a section
     */
    protected function createSection(WebsitePage $page, string $type, array $content, int $order): WebsiteSection
    {
        return WebsiteSection::create([
            'page_id' => $page->id,
            'type' => $type,
            'component_name' => $this->getComponentName($type),
            'content' => $content,
            'settings' => $this->getDefaultSettings($type),
            'is_visible' => true,
            'show_on_mobile' => true,
            'order' => $order,
        ]);
    }
    
    /**
     * Get component name for section type
     */
    protected function getComponentName(string $type): string
    {
        return match($type) {
            'hero' => 'HeroSection',
            'features' => 'FeaturesSection',
            'services' => 'ServicesSection',
            'stats' => 'StatsSection',
            'testimonials' => 'TestimonialsSection',
            'cta' => 'CTASection',
            'team' => 'TeamSection',
            'text-with-image' => 'TextWithImageSection',
            'text-block' => 'TextBlockSection',
            'contact-form' => 'ContactFormSection',
            'contact-info' => 'ContactInfoSection',
            'map' => 'MapSection',
            'menu-category' => 'MenuCategorySection',
            'menu-preview' => 'MenuPreviewSection',
            'portfolio-grid' => 'PortfolioGridSection',
            'portfolio-preview' => 'PortfolioPreviewSection',
            'service-detail' => 'ServiceDetailSection',
            'categories' => 'CategoriesSection',
            default => 'GenericSection',
        };
    }
    
    /**
     * Get default settings for section type
     */
    protected function getDefaultSettings(string $type): array
    {
        return [
            'padding' => ['top' => '80px', 'bottom' => '80px'],
            'background' => ['type' => 'color', 'value' => '#ffffff'],
            'animation' => 'fade-in',
        ];
    }
}
