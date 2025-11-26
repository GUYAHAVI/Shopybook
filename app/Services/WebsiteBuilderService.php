<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Website;
use App\Models\WebsitePage;
use App\Models\WebsiteSection;
use App\Models\WebsiteTheme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebsiteBuilderService
{
    /**
     * Create a new website for a business
     */
    public function createWebsite(Business $business, array $data): array
    {
        try {
            DB::beginTransaction();

            // Check if business already has a website
            if ($business->website) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Business already has a website',
                ];
            }

            // Get theme
            $theme = WebsiteTheme::find($data['theme_id']);
            if (!$theme || !$theme->is_active) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Invalid theme selected',
                ];
            }

            // Create website
            $website = Website::create([
                'business_id' => $business->id,
                'subdomain' => $business->slug,
                'theme_id' => $theme->id,
                'is_active' => true,
                'is_published' => false,
                'settings' => [
                    'site_name' => $data['business_name'] ?? $business->name,
                    'tagline' => $data['tagline'] ?? $business->description,
                    'contact_email' => $data['contact_email'] ?? $business->email,
                    'contact_phone' => $data['contact_phone'] ?? $business->phone,
                ],
            ]);

            // Increment theme usage
            $theme->incrementUsage();

            // Create homepage
            $homepage = $this->createHomepage($website, $data);

            // Create default pages
            $this->createDefaultPages($website, $business);

            // Update business
            $business->update([
                'website_enabled' => true,
                'website_created_at' => now(),
            ]);

            DB::commit();

            Log::info('Website created successfully', [
                'business_id' => $business->id,
                'website_id' => $website->id,
                'subdomain' => $website->subdomain,
            ]);

            return [
                'success' => true,
                'website' => $website,
                'homepage' => $homepage,
                'url' => $website->url,
                'message' => 'Website created successfully!',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Website creation failed', [
                'business_id' => $business->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create website: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create homepage with default sections
     */
    protected function createHomepage(Website $website, array $data): WebsitePage
    {
        $business = $website->business;

        $homepage = WebsitePage::create([
            'website_id' => $website->id,
            'title' => 'Home',
            'slug' => 'home',
            'is_homepage' => true,
            'is_published' => true,
            'show_in_menu' => false,
            'order' => 0,
        ]);

        // Create default sections for homepage
        $this->createHeroSection($homepage, $data);
        $this->createAboutSection($homepage, $data);
        $this->createProductsSection($homepage, $business);
        $this->createContactSection($homepage, $data);

        return $homepage;
    }

    /**
     * Create hero section
     */
    protected function createHeroSection(WebsitePage $page, array $data): WebsiteSection
    {
        $business = $page->website->business;

        return WebsiteSection::create([
            'page_id' => $page->id,
            'type' => 'hero',
            'order' => 1,
            'content' => [
                'heading' => $data['business_name'] ?? $business->name,
                'subheading' => $data['tagline'] ?? $business->description ?? 'Welcome to our website',
                'cta_text' => 'Learn More',
                'cta_link' => '#about',
                'image' => $business->cover_path,
            ],
            'settings' => [
                'padding_top' => 'large',
                'padding_bottom' => 'large',
                'text_align' => 'center',
            ],
            'background_type' => 'gradient',
            'background_value' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'is_visible' => true,
        ]);
    }

    /**
     * Create about section
     */
    protected function createAboutSection(WebsitePage $page, array $data): WebsiteSection
    {
        $business = $page->website->business;

        return WebsiteSection::create([
            'page_id' => $page->id,
            'type' => 'about',
            'order' => 2,
            'content' => [
                'heading' => 'About Us',
                'text' => $data['about_text'] ?? $business->description ?? 'Tell your story here. Share what makes your business special and why customers should choose you.',
                'image' => $business->logo_path,
            ],
            'settings' => [
                'padding_top' => 'medium',
                'padding_bottom' => 'medium',
            ],
            'background_type' => 'color',
            'background_value' => '#ffffff',
            'is_visible' => true,
        ]);
    }

    /**
     * Create products section
     */
    protected function createProductsSection(WebsitePage $page, Business $business): WebsiteSection
    {
        return WebsiteSection::create([
            'page_id' => $page->id,
            'type' => 'products',
            'order' => 3,
            'content' => [
                'heading' => 'Our Products',
                'subheading' => 'Check out what we have to offer',
                'show_price' => true,
                'products_per_row' => 3,
                'max_products' => 6,
                'show_all_button' => true,
            ],
            'settings' => [
                'padding_top' => 'medium',
                'padding_bottom' => 'medium',
            ],
            'background_type' => 'color',
            'background_value' => '#f9fafb',
            'is_visible' => true,
        ]);
    }

    /**
     * Create contact section
     */
    protected function createContactSection(WebsitePage $page, array $data): WebsiteSection
    {
        $business = $page->website->business;

        return WebsiteSection::create([
            'page_id' => $page->id,
            'type' => 'contact',
            'order' => 4,
            'content' => [
                'heading' => 'Get In Touch',
                'subheading' => 'We\'d love to hear from you',
                'show_form' => true,
                'show_map' => false,
                'email' => $data['contact_email'] ?? $business->email,
                'phone' => $data['contact_phone'] ?? $business->phone,
                'address' => $business->address . ', ' . $business->city,
                'whatsapp' => $business->whatsapp_number,
            ],
            'settings' => [
                'padding_top' => 'large',
                'padding_bottom' => 'large',
            ],
            'background_type' => 'color',
            'background_value' => '#ffffff',
            'is_visible' => true,
        ]);
    }

    /**
     * Create default pages (About, Products, Contact)
     */
    protected function createDefaultPages(Website $website, Business $business): void
    {
        // About page
        $aboutPage = WebsitePage::create([
            'website_id' => $website->id,
            'title' => 'About',
            'slug' => 'about',
            'is_homepage' => false,
            'is_published' => true,
            'show_in_menu' => true,
            'order' => 1,
        ]);

        WebsiteSection::create([
            'page_id' => $aboutPage->id,
            'type' => 'about',
            'order' => 1,
            'content' => [
                'heading' => 'About ' . $business->name,
                'text' => $business->description ?? 'Tell your story here...',
                'image' => $business->logo_path,
            ],
            'is_visible' => true,
        ]);

        // Products page
        $productsPage = WebsitePage::create([
            'website_id' => $website->id,
            'title' => 'Products',
            'slug' => 'products',
            'is_homepage' => false,
            'is_published' => true,
            'show_in_menu' => true,
            'order' => 2,
        ]);

        WebsiteSection::create([
            'page_id' => $productsPage->id,
            'type' => 'products',
            'order' => 1,
            'content' => [
                'heading' => 'All Products',
                'subheading' => 'Browse our complete collection',
                'show_price' => true,
                'products_per_row' => 4,
                'max_products' => null, // Show all
            ],
            'is_visible' => true,
        ]);

        // Contact page
        $contactPage = WebsitePage::create([
            'website_id' => $website->id,
            'title' => 'Contact',
            'slug' => 'contact',
            'is_homepage' => false,
            'is_published' => true,
            'show_in_menu' => true,
            'order' => 3,
        ]);

        WebsiteSection::create([
            'page_id' => $contactPage->id,
            'type' => 'contact',
            'order' => 1,
            'content' => [
                'heading' => 'Contact Us',
                'show_form' => true,
                'email' => $business->email,
                'phone' => $business->phone,
                'address' => $business->address . ', ' . $business->city,
            ],
            'is_visible' => true,
        ]);
    }

    /**
     * Update website settings
     */
    public function updateWebsite(Website $website, array $data): array
    {
        try {
            $website->update([
                'colors' => $data['colors'] ?? $website->colors,
                'fonts' => $data['fonts'] ?? $website->fonts,
                'settings' => array_merge($website->settings ?? [], $data['settings'] ?? []),
                'seo_settings' => array_merge($website->seo_settings ?? [], $data['seo_settings'] ?? []),
            ]);

            return [
                'success' => true,
                'website' => $website,
                'message' => 'Website updated successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Website update failed', [
                'website_id' => $website->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update website',
            ];
        }
    }

    /**
     * Create a new page
     */
    public function createPage(Website $website, array $data): array
    {
        try {
            $page = WebsitePage::create([
                'website_id' => $website->id,
                'title' => $data['title'],
                'slug' => $data['slug'] ?? Str::slug($data['title']),
                'description' => $data['description'] ?? null,
                'is_published' => $data['is_published'] ?? false,
                'show_in_menu' => $data['show_in_menu'] ?? true,
            ]);

            return [
                'success' => true,
                'page' => $page,
                'message' => 'Page created successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Page creation failed', [
                'website_id' => $website->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create page',
            ];
        }
    }

    /**
     * Update page
     */
    public function updatePage(WebsitePage $page, array $data): array
    {
        try {
            $page->update($data);

            return [
                'success' => true,
                'page' => $page->fresh(),
                'message' => 'Page updated successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Page update failed', [
                'page_id' => $page->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update page',
            ];
        }
    }

    /**
     * Create a new section
     */
    public function createSection(WebsitePage $page, array $data): array
    {
        try {
            $section = WebsiteSection::create([
                'page_id' => $page->id,
                'type' => $data['type'],
                'content' => $data['content'] ?? [],
                'settings' => $data['settings'] ?? [],
                'background_type' => $data['background_type'] ?? 'color',
                'background_value' => $data['background_value'] ?? '#ffffff',
                'is_visible' => $data['is_visible'] ?? true,
            ]);

            return [
                'success' => true,
                'section' => $section,
                'message' => 'Section added successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Section creation failed', [
                'page_id' => $page->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to add section',
            ];
        }
    }

    /**
     * Update section
     */
    public function updateSection(WebsiteSection $section, array $data): array
    {
        try {
            $section->update($data);

            return [
                'success' => true,
                'section' => $section->fresh(),
                'message' => 'Section updated successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Section update failed', [
                'section_id' => $section->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update section',
            ];
        }
    }

    /**
     * Delete section
     */
    public function deleteSection(WebsiteSection $section): array
    {
        try {
            $section->delete();

            return [
                'success' => true,
                'message' => 'Section deleted successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Section deletion failed', [
                'section_id' => $section->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to delete section',
            ];
        }
    }

    /**
     * Reorder sections
     */
    public function reorderSections(WebsitePage $page, array $sectionIds): array
    {
        try {
            DB::transaction(function () use ($page, $sectionIds) {
                foreach ($sectionIds as $index => $sectionId) {
                    WebsiteSection::where('id', $sectionId)
                        ->where('page_id', $page->id)
                        ->update(['order' => $index + 1]);
                }
            });

            return [
                'success' => true,
                'message' => 'Sections reordered successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Section reordering failed', [
                'page_id' => $page->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to reorder sections',
            ];
        }
    }

    /**
     * Publish website
     */
    public function publishWebsite(Website $website): array
    {
        try {
            $website->publish();

            return [
                'success' => true,
                'message' => 'Website published successfully!',
                'url' => $website->url,
            ];

        } catch (\Exception $e) {
            Log::error('Website publish failed', [
                'website_id' => $website->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to publish website',
            ];
        }
    }

    /**
     * Change theme
     */
    public function changeTheme(Website $website, int $themeId): array
    {
        try {
            $theme = WebsiteTheme::find($themeId);
            
            if (!$theme || !$theme->is_active) {
                return [
                    'success' => false,
                    'message' => 'Invalid theme selected',
                ];
            }

            $website->update(['theme_id' => $themeId]);
            $theme->incrementUsage();

            return [
                'success' => true,
                'website' => $website->fresh(),
                'message' => 'Theme changed successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Theme change failed', [
                'website_id' => $website->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to change theme',
            ];
        }
    }
}

