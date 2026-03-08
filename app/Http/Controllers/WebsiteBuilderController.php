<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Website;
use App\Models\WebsitePage;
use App\Models\WebsiteSection;
use App\Models\WebsiteTheme;
use App\Services\WebsiteBuilderService;
use App\Services\ClaudeAPIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WebsiteBuilderController extends Controller
{
    protected $websiteBuilderService;
    protected $claudeAPIService;

    public function __construct(WebsiteBuilderService $websiteBuilderService, ClaudeAPIService $claudeAPIService)
    {
        $this->middleware('auth');
        $this->websiteBuilderService = $websiteBuilderService;
        $this->claudeAPIService = $claudeAPIService;
    }

    /**
     * Show website builder dashboard
     */
    public function index()
    {
        // Clear preview session so owner links revert to non-editor mode
        session()->forget('website_preview_id');

        $business = Auth::user()->business;
        
        if (!$business) {
            return redirect()->route('business.create')->with('error', 'Please create a business first');
        }

        $website = $business->website;

        // If no website exists, redirect to configurator
        if (!$website) {
            return redirect()->route('website-configurator.step1');
        }

        // Website exists — go straight to the editor preview
        return redirect()->route('website.builder.preview');
    }

    /**
     * Show setup wizard
     */
    public function showSetup()
    {
        $business = Auth::user()->business;

        return view('website-builder.setup', compact('business'));

        return view('website-builder.setup', compact('themes', 'business'));
    }

    /**
     * Create new website
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'theme_id' => 'required|exists:website_themes,id',
            'business_name' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'about_text' => 'nullable|string',
        ]);

        $business = Auth::user()->business;
        $result = $this->websiteBuilderService->createWebsite($business, $validated);

        // Return JSON response for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'redirect_url' => route('website.builder.index')
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        }

        // Fallback for non-AJAX requests
        if ($result['success']) {
            return redirect()
                ->route('website.builder.index')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }

    /**
     * Show page editor
     */
    public function editPage(WebsitePage $page)
    {
        $this->authorize('update', $page->website);

        $website = $page->website;
        $sections = $page->sections()->orderBy('order')->get();
        $availableSectionTypes = $this->getAvailableSectionTypes();

        return view('website-builder.page-editor', compact('page', 'website', 'sections', 'availableSectionTypes'));
    }

    /**
     * Update website settings
     */
    public function updateSettings(Request $request)
    {
        $business = Auth::user()->business;
        $website = $business->website;

        if (!$website) {
            return response()->json(['error' => 'Website not found'], 404);
        }

        $validated = $request->validate([
            'colors' => 'nullable|array',
            'fonts' => 'nullable|array',
            'settings' => 'nullable|array',
            'seo_settings' => 'nullable|array',
        ]);

        $result = $this->websiteBuilderService->updateWebsite($website, $validated);

        return response()->json($result);
    }

    /**
     * Create new page
     */
    public function storePage(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_published' => 'nullable|boolean',
            'show_in_menu' => 'nullable|boolean',
        ]);

        $business = Auth::user()->business;
        $website = $business->website;

        $result = $this->websiteBuilderService->createPage($website, $validated);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    /**
     * Update page
     */
    public function updatePage(Request $request, WebsitePage $page)
    {
        $this->authorize('update', $page->website);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_published' => 'sometimes|boolean',
            'show_in_menu' => 'sometimes|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $result = $this->websiteBuilderService->updatePage($page, $validated);

        return response()->json($result);
    }

    /**
     * Delete page
     */
    public function deletePage(WebsitePage $page)
    {
        $this->authorize('update', $page->website);

        if ($page->is_homepage) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete homepage',
            ], 400);
        }

        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully',
        ]);
    }

    /**
     * Duplicate page
     */
    public function duplicatePage(WebsitePage $page)
    {
        $this->authorize('update', $page->website);

        $newPage = $page->duplicate();

        return response()->json([
            'success' => true,
            'page' => $newPage,
            'message' => 'Page duplicated successfully',
        ]);
    }

    /**
     * Create section
     */
    public function storeSection(Request $request, WebsitePage $page)
    {
        $this->authorize('update', $page->website);

        $validated = $request->validate([
            'type' => 'required|string',
            'content' => 'nullable|array',
            'settings' => 'nullable|array',
            'background_type' => 'nullable|string',
            'background_value' => 'nullable|string',
            'is_visible' => 'nullable|boolean',
        ]);

        $result = $this->websiteBuilderService->createSection($page, $validated);

        return response()->json($result);
    }

    /**
     * Update section
     */
    public function updateSection(Request $request, WebsiteSection $section)
    {
        $this->authorize('update', $section->page->website);

        $validated = $request->validate([
            'type' => 'sometimes|string',
            'content' => 'nullable|array',
            'settings' => 'nullable|array',
            'background_type' => 'nullable|string',
            'background_value' => 'nullable|string',
            'is_visible' => 'nullable|boolean',
            'order' => 'sometimes|integer',
        ]);

        $result = $this->websiteBuilderService->updateSection($section, $validated);

        return response()->json($result);
    }

    /**
     * Delete section
     */
    public function deleteSection(WebsiteSection $section)
    {
        $this->authorize('update', $section->page->website);

        $result = $this->websiteBuilderService->deleteSection($section);

        return response()->json($result);
    }

    /**
     * Reorder sections
     */
    public function reorderSections(Request $request, WebsitePage $page)
    {
        $this->authorize('update', $page->website);

        $validated = $request->validate([
            'section_ids' => 'required|array',
            'section_ids.*' => 'exists:website_sections,id',
        ]);

        $result = $this->websiteBuilderService->reorderSections($page, $validated['section_ids']);

        return response()->json($result);
    }

    /**
     * Move section up
     */
    public function moveSectionUp(WebsiteSection $section)
    {
        $this->authorize('update', $section->page->website);

        $success = $section->moveUp();

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Section moved up' : 'Cannot move further up',
        ]);
    }

    /**
     * Move section down
     */
    public function moveSectionDown(WebsiteSection $section)
    {
        $this->authorize('update', $section->page->website);

        $success = $section->moveDown();

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Section moved down' : 'Cannot move further down',
        ]);
    }

    /**
     * Publish website
     */
    public function publish()
    {
        $business = Auth::user()->business;
        $website = $business->website;

        if (!$website) {
            return response()->json(['error' => 'Website not found'], 404);
        }

        $result = $this->websiteBuilderService->publishWebsite($website);

        return response()->json($result);
    }

    /**
     * Update website colors and fonts
     */
    public function updateCustomization(\Illuminate\Http\Request $request)
    {
        $business = Auth::user()->business;
        $website  = $business->website;

        if (!$website) {
            return response()->json(['error' => 'Website not found'], 404);
        }

        $request->validate([
            'primary_color'   => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'secondary_color' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'accent_color'    => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'heading_font'    => 'required|string|max:80',
            'body_font'       => 'required|string|max:80',
        ]);

        $website->update([
            'colors' => [
                'primary'    => $request->primary_color,
                'secondary'  => $request->secondary_color,
                'accent'     => $request->accent_color,
                'background' => $request->input('background_color', '#ffffff'),
                'text'       => $request->input('text_color', '#212121'),
            ],
            'fonts' => [
                'heading' => $request->heading_font,
                'body'    => $request->body_font,
            ],
        ]);

        return response()->json(['success' => true, 'message' => 'Customization saved!']);
    }

    /**
     * Unpublish website
     */
    public function unpublish()
    {
        $business = Auth::user()->business;
        $website = $business->website;

        if (!$website) {
            return response()->json(['error' => 'Website not found'], 404);
        }

        $website->unpublish();

        return response()->json([
            'success' => true,
            'message' => 'Website unpublished successfully',
        ]);
    }

    /**
     * Change theme
     */
    public function changeTheme(Request $request)
    {
        $validated = $request->validate([
            'theme_id' => 'required|exists:website_themes,id',
        ]);

        $business = Auth::user()->business;
        $website = $business->website;

        $result = $this->websiteBuilderService->changeTheme($website, $validated['theme_id']);

        return response()->json($result);
    }

    /**
     * Preview website
     */
    public function previewPage(WebsitePage $page)
    {
        $business = Auth::user()->business;
        $website  = $business ? $business->website : null;

        // Security: ensure the page belongs to this user's website
        if (!$website || $page->website_id !== $website->id) {
            abort(403);
        }

        session(['website_preview_id' => $website->id]);
        return $this->showPreviewPage($page, true);
    }

    public function preview()
    {
        $business = Auth::user()->business;
        $website = $business->website;

        if (!$website) {
            return redirect()->route('website.builder.index')->with('error', 'Website not found');
        }

        // Get homepage
        $homepage = $website->homepage;

        if (!$homepage) {
            return redirect()->route('website.builder.index')
                ->with('error', 'No homepage found. Please create pages first.');
        }

        // Store preview flag in session so secondary pages (About, Services, etc.)
        // also show the editor toolbar when navigated to via nav links.
        session(['website_preview_id' => $website->id]);

        // Render directly at the /website-builder/preview URL (no redirect).
        // Nav links in the template append ?preview=1 and PublicWebsiteController
        // checks the session flag above to keep the editor on those pages too.
        return $this->showPreviewPage($homepage, true);
    }

    /**
     * Preview a specific theme
     */
    public function previewTheme(Request $request)
    {
        $validated = $request->validate([
            'theme_id' => 'required|exists:website_themes,id',
        ]);

        $theme = WebsiteTheme::findOrFail($validated['theme_id']);
        $business = Auth::user()->business;

        return $this->renderThemePreview($theme, $business);
    }

    /**
     * Preview a specific theme (GET method for direct links)
     */
    public function previewThemeGet($themeId)
    {
        $theme = WebsiteTheme::findOrFail($themeId);
        $business = Auth::user()->business;
        
        if (!$business) {
            return redirect()->route('business.create')->with('error', 'Please create a business first');
        }

        return $this->renderThemePreview($theme, $business);
    }

    /**
     * Render theme preview (shared logic)
     */
    protected function renderThemePreview(WebsiteTheme $theme, Business $business)
    {
        // Create a temporary mock website for preview
        $mockWebsite = new Website([
            'business_id' => $business->id,
            'subdomain' => $business->slug ?? 'preview',
            'theme_id' => $theme->id,
            'is_published' => false,
            'settings' => [
                'site_name' => $business->name ?? 'Your Business Name',
                'tagline' => $business->description ?? 'Empowering Your Business to Grow and Succeed',
                'contact_email' => $business->email ?? 'contact@yourbusiness.com',
                'contact_phone' => $business->phone ?? '+1 (555) 123-4567',
                'about_text' => 'We are a leading business dedicated to providing exceptional products and services to our valued customers. Our mission is to deliver excellence in everything we do.',
            ],
            'colors' => $theme->default_colors ?? [
                'primary' => '#4F46E5',
                'secondary' => '#6366F1',
                'accent' => '#F59E0B',
                'background' => '#FFFFFF',
                'text' => '#1F2937',
            ],
            'fonts' => $theme->default_fonts ?? [
                'heading' => 'Inter',
                'body' => 'Inter',
            ],
        ]);
        
        $mockWebsite->setRelation('business', $business);
        $mockWebsite->setRelation('theme', $theme);

        // Create mock homepage
        $mockPage = new WebsitePage([
            'title' => 'Home',
            'slug' => 'home',
            'is_homepage' => true,
            'is_published' => true,
        ]);

        // Create mock hero section
        $mockSection = new WebsiteSection([
            'type' => 'hero',
            'order' => 1,
            'is_visible' => true,
            'content' => [
                'heading' => 'Welcome to ' . ($business->name ?? 'Your Business'),
                'subheading' => $business->description ?? 'Discover amazing products and services tailored for your needs',
                'cta_text' => 'Get Started',
                'cta_link' => '#',
                'background_image' => null,
            ],
        ]);

        $mockPage->setRelation('sections', collect([$mockSection]));
        $mockPage->setRelation('website', $mockWebsite);

        return view('website-builder.theme-preview', [
            'website' => $mockWebsite,
            'page' => $mockPage,
            'theme' => $theme,
            'isPreview' => true,
            'menuPages' => collect([]),
        ]);
    }

    /**
     * Show preview page (shared logic)
     */
    protected function showPreviewPage(WebsitePage $page, bool $isPreview = false)
    {
        $website   = $page->website;
        $menuPages = $website->menuPages;
        $products  = $website->business->products()
                        ->where('stock_quantity', '>', 0)
                        ->limit(12)
                        ->get();

        return view('public-website.page', [
            'website'   => $website,
            'page'      => $page,
            'menuPages' => $menuPages,
            'products'  => $products,
            'isPreview' => $isPreview,
        ]);
    }

    /**
     * Get available section types
     */
    protected function getAvailableSectionTypes(): array
    {
        return [
            [
                'type' => 'hero',
                'name' => 'Hero Section',
                'description' => 'Large banner with heading and call-to-action',
                'icon' => '🎯',
            ],
            [
                'type' => 'about',
                'name' => 'About Section',
                'description' => 'Tell your story with text and image',
                'icon' => 'ℹ️',
            ],
            [
                'type' => 'features',
                'name' => 'Features',
                'description' => 'Showcase key features or benefits',
                'icon' => '⭐',
            ],
            [
                'type' => 'services',
                'name' => 'Services',
                'description' => 'List your services',
                'icon' => '🛠️',
            ],
            [
                'type' => 'products',
                'name' => 'Products',
                'description' => 'Display your products',
                'icon' => '🛍️',
            ],
            [
                'type' => 'gallery',
                'name' => 'Gallery',
                'description' => 'Image gallery grid',
                'icon' => '🖼️',
            ],
            [
                'type' => 'testimonials',
                'name' => 'Testimonials',
                'description' => 'Customer reviews and testimonials',
                'icon' => '💬',
            ],
            [
                'type' => 'team',
                'name' => 'Team',
                'description' => 'Introduce your team members',
                'icon' => '👥',
            ],
            [
                'type' => 'contact',
                'name' => 'Contact',
                'description' => 'Contact form and information',
                'icon' => '📧',
            ],
            [
                'type' => 'cta',
                'name' => 'Call to Action',
                'description' => 'Encourage visitors to take action',
                'icon' => '🎬',
            ],
        ];
    }

    /**
     * AI: Recommend best theme for business
     */
    public function recommendTheme(Request $request)
    {
        $business = Auth::user()->business;
        
        if (!$business) {
            return response()->json(['error' => 'No business found'], 404);
        }

        $themes = WebsiteTheme::active()->get();
        
        $businessData = [
            'name' => $business->name,
            'type' => $business->business_type ?? 'general',
            'description' => $business->description,
            'location' => $business->address ?? 'Kenya',
        ];

        $recommendations = $this->claudeAPIService->recommendTheme($businessData, $themes);

        if (!$recommendations) {
            return response()->json(['error' => 'Unable to generate recommendations'], 500);
        }

        // Enrich recommendations with theme details
        $enrichedRecommendations = collect($recommendations)->map(function($rec) use ($themes) {
            $theme = $themes->firstWhere('slug', $rec['slug']);
            if ($theme) {
                $rec['theme'] = [
                    'id' => $theme->id,
                    'name' => $theme->name,
                    'category' => $theme->category,
                    'style' => $theme->style,
                    'thumbnail' => $theme->thumbnail_path,
                ];
            }
            return $rec;
        })->filter(fn($rec) => isset($rec['theme']));

        return response()->json([
            'recommendations' => $enrichedRecommendations,
            'business' => $businessData,
        ]);
    }

    /**
     * AI: Generate section content
     */
    public function generateSectionContent(Request $request)
    {
        $request->validate([
            'section_type' => 'required|string',
            'website_id' => 'required|exists:websites,id',
        ]);

        $website = Website::findOrFail($request->website_id);
        $this->authorize('update', $website);

        $business = $website->business;
        $businessData = [
            'name' => $business->name,
            'type' => $business->business_type ?? 'general',
            'description' => $business->description,
            'location' => $business->address ?? 'Kenya',
        ];

        $existingContent = $request->input('existing_content', null);
        
        $generatedContent = $this->claudeAPIService->generateSectionContent(
            $request->section_type,
            $businessData,
            $existingContent
        );

        if (!$generatedContent) {
            return response()->json(['error' => 'Unable to generate content'], 500);
        }

        return response()->json([
            'content' => $generatedContent,
            'section_type' => $request->section_type,
        ]);
    }

    /**
     * AI: Generate SEO metadata for page
     */
    public function generateSEO(Request $request)
    {
        $request->validate([
            'page_id' => 'required|exists:website_pages,id',
        ]);

        $page = WebsitePage::findOrFail($request->page_id);
        $website = $page->website;
        $this->authorize('update', $website);

        $business = $website->business;
        
        $pageData = [
            'name' => $page->title,
            'slug' => $page->slug,
        ];

        $businessData = [
            'name' => $business->name,
            'type' => $business->business_type ?? 'general',
            'description' => $business->description,
            'location' => $business->address ?? 'Kenya',
        ];

        $seoData = $this->claudeAPIService->generateSEOMetadata($pageData, $businessData);

        if (!$seoData) {
            return response()->json(['error' => 'Unable to generate SEO metadata'], 500);
        }

        // Optionally auto-update the page
        if ($request->input('auto_apply', false)) {
            $page->update([
                'meta_description' => $seoData['meta_description'] ?? $page->meta_description,
                'meta_keywords' => $seoData['meta_keywords'] ?? $page->meta_keywords,
            ]);
        }

        return response()->json([
            'seo' => $seoData,
            'page' => $page,
        ]);
    }

    /**
     * AI: Get website building guidance
     */
    public function getGuidance(Request $request)
    {
        $business = Auth::user()->business;
        
        if (!$business) {
            return response()->json(['error' => 'No business found'], 404);
        }

        $website = $business->website;
        $selectedTheme = null;
        
        if ($website && $website->theme) {
            $selectedTheme = $website->theme->name;
        }

        $businessData = [
            'name' => $business->name,
            'type' => $business->business_type ?? 'general',
            'description' => $business->description,
        ];

        $guidance = $this->claudeAPIService->generateWebsiteGuidance($businessData, $selectedTheme);

        if (!$guidance) {
            return response()->json(['error' => 'Unable to generate guidance'], 500);
        }

        return response()->json([
            'guidance' => $guidance,
            'business' => $businessData,
            'theme' => $selectedTheme,
        ]);
    }

    /**
     * AI: Suggest image prompts for sections
     */
    public function suggestImages(Request $request)
    {
        $request->validate([
            'section_type' => 'required|string',
            'website_id' => 'required|exists:websites,id',
        ]);

        $website = Website::findOrFail($request->website_id);
        $this->authorize('update', $website);

        $business = $website->business;
        $businessData = [
            'name' => $business->name,
            'type' => $business->business_type ?? 'general',
        ];

        $imagePrompts = $this->claudeAPIService->suggestImagePrompts(
            $request->section_type,
            $businessData
        );

        if (!$imagePrompts) {
            return response()->json(['error' => 'Unable to generate image suggestions'], 500);
        }

        return response()->json([
            'prompts' => $imagePrompts,
            'section_type' => $request->section_type,
        ]);
    }

    /**
     * AI: Generate an image for a website section using Pollinations.AI (same as marketing module)
     */
    public function generateSectionImage(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:website_sections,id',
            'prompt'     => 'nullable|string|max:500',
        ]);

        $section = \App\Models\WebsiteSection::with('page.website.business')->findOrFail($request->section_id);
        $website = $section->page->website;
        $this->authorize('update', $website);

        $business  = $website->business;
        $content   = $section->getContentWithDefaults();

        // Build the prompt: use user-supplied prompt → stored image_query → smart default
        $userPrompt = $request->prompt;
        if (!$userPrompt) {
            $imageQuery = $content['image_query'] ?? null;
            if ($imageQuery) {
                $userPrompt = $imageQuery;
            } else {
                // Smart default from section type + business
                $typeDefaults = [
                    'hero'         => "professional storefront display for {$business->name}, {$business->business_type} business",
                    'about'        => "professional team or office interior for {$business->name}",
                    'services'     => "professional service delivery, {$business->business_type}",
                    'features'     => "quality product detail, {$business->business_type}",
                    'testimonials' => "happy satisfied customers, professional setting",
                    'products'     => "product display, {$business->business_type} store",
                    'contact'      => "welcoming business environment, {$business->business_type}",
                ];
                $userPrompt = $typeDefaults[$section->type] ?? "{$business->business_type} business, professional photo";
            }
        }

        // Determine size based on section type
        $size = ($section->type === 'hero') ? '1200x600' : '800x500';

        try {
            $imageData = $this->claudeAPIService->generateMarketingImage(
                $userPrompt,
                'realistic',
                $size,
                $business->name,
                $business->business_type ?? 'general'
            );

            if (!$imageData || !isset($imageData['public_url'])) {
                return response()->json(['error' => 'Image generation failed. Please try again.'], 500);
            }

            // Save the local path into section content
            $content['image'] = $imageData['relative_path'];
            $section->content = $content;
            $section->save();

            return response()->json([
                'success'    => true,
                'image_url'  => $imageData['public_url'],
                'local_path' => $imageData['relative_path'],
                'section_id' => $section->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Website section image generation failed', [
                'section_id' => $section->id,
                'error'      => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Image generation failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Upload a custom image for a website section
     */
    public function uploadSectionImage(Request $request, \App\Models\WebsiteSection $section)
    {
        $request->validate(['image' => 'required|image|max:5120']);

        $website = $section->page->website;
        $this->authorize('update', $website);

        $path = $request->file('image')->store(
            'website-images/' . $website->subdomain,
            'public'
        );

        $content = $section->content ?? [];
        $content['image'] = $path;
        $section->content = $content;
        $section->save();

        return response()->json([
            'success'   => true,
            'image_url' => asset('storage/' . $path),
            'local_path'=> $path,
            'section_id'=> $section->id,
        ]);
    }

    /**
     * Upload website logo
     */
    public function uploadLogo(Request $request)
    {
        $request->validate(['logo' => 'required|image|mimes:jpg,jpeg,png,gif,svg,webp|max:5120']);

        $business = Auth::user()->business;
        $website  = $business->website;

        if (!$website) {
            return response()->json(['error' => 'Website not found'], 404);
        }

        $path = $request->file('logo')->store(
            'website-logos/' . $website->subdomain,
            'public'
        );

        $website->update(['logo_path' => $path]);

        return response()->json([
            'success'  => true,
            'logo_url' => asset('storage/' . $path),
        ]);
    }

    /**
     * AI: Auto-build complete website (Enterprise Only)
     */
    public function autoBuildWebsite(Request $request)
    {
        // Claude AI can take 2-3 minutes to generate a full website; remove PHP's 60s limit
        set_time_limit(300);

        try {
            Log::info('Auto-build website started', ['request_data' => $request->all()]);
            
            $business = Auth::user()->business;
            
            if (!$business) {
                Log::warning('Auto-build failed: No business found for user', ['user_id' => Auth::id()]);
                return response()->json(['error' => 'No business found'], 404);
            }

            Log::info('Business found', ['business_id' => $business->id, 'plan' => $business->plan]);

            // Check if business has enterprise plan
            if (!$business->isEnterprise()) {
                Log::warning('Auto-build blocked: Not enterprise plan', [
                    'business_id' => $business->id,
                    'current_plan' => $business->plan
                ]);
                return response()->json([
                    'error' => 'This feature is only available for Enterprise subscribers',
                    'upgrade_required' => true
                ], 403);
            }

            // Check if website already exists
            if ($business->website) {
                Log::warning('Auto-build blocked: Website already exists', [
                    'business_id' => $business->id,
                    'website_id' => $business->website->id
                ]);
                return response()->json([
                    'error' => 'Website already exists. Please delete existing website first.',
                    'existing_website' => true
                ], 409);
            }

            $request->validate([
                'theme_id' => 'required|exists:website_themes,id',
            ]);

            $theme = WebsiteTheme::findOrFail($request->theme_id);
            Log::info('Theme selected', ['theme_id' => $theme->id, 'theme_name' => $theme->name]);

            // Gather business data
            $businessData = [
                'name' => $business->name,
                'type' => $business->business_type ?? 'general',
                'description' => $business->description,
                'location' => $business->address ?? 'Kenya',
                'email' => $business->email,
                'phone' => $business->phone,
            ];

            Log::info('Calling Claude API to generate website structure', ['business_data' => $businessData]);

            // Generate complete website structure with AI
            $websiteStructure = $this->claudeAPIService->generateCompleteWebsite(
                $businessData,
                $theme->name
            );

            if (!$websiteStructure || !isset($websiteStructure['pages'])) {
                Log::error('Claude API failed to generate website structure', [
                    'website_structure' => $websiteStructure
                ]);
                return response()->json([
                    'error' => 'Unable to generate website structure. Please try again.',
                    'details' => 'AI service did not return valid structure'
                ], 500);
            }

            Log::info('Website structure generated', [
                'pages_count' => count($websiteStructure['pages'] ?? [])
            ]);

            // Hero and About sections use the template's built-in gradient visuals (no external image needed).
            // Other sections (services, features, etc.) can use loremflickr for background accents.
            $businessSlug = \Str::slug($businessData['name']);
            $skipImageTypes = ['hero', 'about']; // These have beautiful built-in gradient fallbacks
            $typeKeywordFallback = [
                'services'     => $businessData['type'] . ',services,professional',
                'features'     => $businessData['type'] . ',quality,detail',
                'stats'        => $businessData['type'] . ',business,success',
                'testimonials' => 'happy,customers,satisfied',
                'cta'          => $businessData['type'] . ',lifestyle',
                'products'     => $businessData['type'] . ',products,retail',
                'contact'      => 'office,building,interior',
            ];
            foreach ($websiteStructure['pages'] as &$pageData) {
                foreach ($pageData['sections'] as &$sectionData) {
                    $type = $sectionData['type'] ?? 'general';
                    // Skip hero/about — let them use the template's gradient visual box
                    if (in_array($type, $skipImageTypes)) {
                        unset($sectionData['content']['image']);
                        continue;
                    }
                    $seed = abs(crc32($businessSlug . $type)) % 9999;
                    $rawQuery = $sectionData['content']['image_query']
                        ?? ($typeKeywordFallback[$type] ?? $businessData['type'] . ',professional');
                    // Encode each word individually — commas must stay literal in the URL path
                    $words = array_slice(preg_split('/[\s,]+/', strtolower(trim($rawQuery))), 0, 5);
                    $words = array_map('rawurlencode', array_filter($words));
                    $keywords = implode(',', $words);
                    $sectionData['content']['image'] = "https://loremflickr.com/800/500/{$keywords}?lock={$seed}";
                }
            }
            unset($pageData, $sectionData);

            DB::beginTransaction();

            // Create website
            $website = Website::create([
                'business_id' => $business->id,
                'subdomain' => $business->slug,
                'theme_id' => $theme->id,
                'colors' => $theme->default_colors ?? [],
                'fonts' => $theme->default_fonts ?? [],
                'settings' => [
                    'business_name' => $business->name,
                    'tagline' => "Welcome to {$business->name}",
                    'contact_email' => $business->email,
                    'contact_phone' => $business->phone,
                ],
                'seo_settings' => [
                    'site_title' => $business->name,
                    'site_description' => $business->description ?? "Welcome to {$business->name}",
                ],
                'logo_path' => $business->logo_path,
                'is_published' => true,
            ]);

            $createdPages = [];
            $pageOrder = 1;

            // Create all pages with sections
            foreach ($websiteStructure['pages'] as $pageData) {
                $isHomepage = $pageData['is_homepage'] ?? false;
                $page = WebsitePage::create([
                    'website_id' => $website->id,
                    'title' => $pageData['title'] ?? 'Untitled Page',
                    'slug' => $pageData['slug'] ?? \Str::slug($pageData['title'] ?? 'page'),
                    'meta_description' => $pageData['meta_description'] ?? '',
                    'meta_keywords' => $pageData['meta_keywords'] ?? '',
                    'is_published' => true,
                    'is_homepage' => $isHomepage,
                    'show_in_menu' => true,
                    'order' => $pageOrder++,
                ]);

                $sectionOrder = 1;
                $createdSections = [];

                // Create sections for this page
                if (isset($pageData['sections']) && is_array($pageData['sections'])) {
                    foreach ($pageData['sections'] as $sectionData) {
                        if (isset($sectionData['type']) && isset($sectionData['content'])) {
                            $section = WebsiteSection::create([
                                'page_id' => $page->id, // Fixed: changed from 'website_page_id' to 'page_id'
                                'type' => $sectionData['type'],
                                'content' => $sectionData['content'],
                                'settings' => $sectionData['settings'] ?? [],
                                'is_visible' => true,
                                'order' => $sectionOrder++,
                            ]);

                            $createdSections[] = [
                                'id' => $section->id,
                                'type' => $section->type,
                            ];
                        }
                    }
                }

                $createdPages[] = [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'sections_count' => count($createdSections),
                    'sections' => $createdSections,
                ];
            }

            DB::commit();

            Log::info('Website auto-build completed successfully', [
                'website_id' => $website->id,
                'pages_count' => count($createdPages)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Complete website auto-built successfully!',
                'website' => [
                    'id' => $website->id,
                    'subdomain' => $website->subdomain,
                    'theme' => $theme->name,
                    'pages_count' => count($createdPages),
                ],
                'pages' => $createdPages,
                'preview_url' => route('website.builder.preview'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auto-Build Website Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'error' => 'Failed to create website. Please try again.',
                'details' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Delete entire website
     */
    public function deleteWebsite(Request $request)
    {
        try {
            $business = Auth::user()->business;
            
            if (!$business) {
                Log::warning('Delete Website Failed: No business found for user', ['user_id' => Auth::id()]);
                return response()->json(['error' => 'No business found'], 404);
            }

            $website = $business->website;

            if (!$website) {
                Log::warning('Delete Website Failed: No website found', ['business_id' => $business->id]);
                return response()->json(['error' => 'No website found to delete'], 404);
            }

            Log::info('Starting website deletion', [
                'business_id' => $business->id,
                'website_id' => $website->id,
                'pages_count' => $website->pages()->count()
            ]);

            DB::beginTransaction();

            // Delete all sections first
            foreach ($website->pages as $page) {
                $page->sections()->delete();
            }

            // Delete all pages
            $website->pages()->delete();

            // Delete the website
            $website->delete();

            DB::commit();

            Log::info('Website deleted successfully', ['business_id' => $business->id]);

            // Set flag to allow user to recreate website
            session(['website_deleted' => true]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Website deleted successfully',
                    'redirect_url' => route('website-configurator.step1')
                ]);
            }

            return redirect()
                ->route('website-configurator.step1')
                ->with('success', 'Website deleted successfully. You can now create a new one.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Website Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Failed to delete website: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to delete website. Please try again.');
        }
    }
}

