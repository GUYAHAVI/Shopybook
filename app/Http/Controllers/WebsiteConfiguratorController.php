<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Website;
use App\Models\WebsitePage;
use App\Models\WebsiteSection;
use App\Models\WebsiteTheme;
use App\Services\WebsiteBuilderService;
use App\Services\WebsiteContentService;
use App\Services\ClaudeAPIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebsiteConfiguratorController extends Controller
{
    protected $websiteBuilderService;
    protected $contentService;
    protected $claudeService;

    public function __construct(
        WebsiteBuilderService $websiteBuilderService,
        WebsiteContentService $contentService,
        ClaudeAPIService $claudeService
    ) {
        $this->middleware('auth');
        $this->websiteBuilderService = $websiteBuilderService;
        $this->contentService = $contentService;
        $this->claudeService = $claudeService;
    }

    /**
     * Step 1: Choose website purpose
     */
    public function step1()
    {
        $business = Auth::user()->business;
        
        if (!$business) {
            return redirect()->route('business.create')
                ->with('error', 'Please create a business first');
        }

        // Check if website already exists (unless coming from delete)
        if ($business->website && !session()->has('website_deleted')) {
            return redirect()->route('website.builder.index')
                ->with('info', 'You already have a website. Edit it from the dashboard or delete it to create a new one.');
        }

        // Clear the website_deleted flag if it exists
        session()->forget('website_deleted');

        // Get available themes for AI build
        $themes = \App\Models\WebsiteTheme::active()->get();

        return view('website-configurator.step1', compact('business', 'themes'));
    }

    /**
     * Handle Step 1 submission
     */
    public function step1Submit(Request $request)
    {
        try {
            Log::info('Step 1 submission started', ['user_id' => Auth::id()]);

            $validated = $request->validate([
                'website_type' => 'required|in:business,store,service,restaurant,portfolio,blog',
            ]);

            Log::info('Step 1 validation passed', ['validated' => $validated]);

            // Store in session (both formats for compatibility)
            session([
                'website_type' => $validated['website_type'],
                'website_config' => [
                    'website_type' => $validated['website_type'],
                ],
            ]);

            // Force save session
            session()->save();

            Log::info('Step 1 session saved', [
                'website_type' => session('website_type'),
                'website_config' => session('website_config'),
            ]);

            return redirect()->route('website-configurator.step2')
                ->with('success', 'Step 1 completed successfully!');
                
        } catch (\Exception $e) {
            Log::error('Step 1 submission error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Show Step 2: Business description
     */
    public function step2View(Request $request)
    {
        // Ensure step 1 is complete
        if (!session()->has('website_type')) {
            return redirect()->route('website-configurator.step1')
                ->with('error', 'Please complete step 1 first');
        }

        $business = Auth::user()->business;
        $websiteType = session('website_type');

        return view('website-configurator.step2', [
            'business' => $business,
            'websiteType' => $websiteType,
        ]);
    }

    /**
     * Step 2: Describe your business (submission handler)
     */
    public function step2(Request $request)
    {
        try {
            Log::info('Step 2 submission started', [
                'user_id' => Auth::id(),
                'session_website_type' => session('website_type'),
            ]);

            $validated = $request->validate([
                'business_name' => 'required|string|max:255',
                'business_description' => 'nullable|string|max:500',
            ]);

            Log::info('Step 2 validation passed', ['validated' => $validated]);

            // Store in session (both formats for compatibility)
            session([
                'business_name' => $validated['business_name'],
                'business_description' => $validated['business_description'],
            ]);

            $config = session('website_config', []);
            $config['business_name'] = $validated['business_name'];
            $config['business_description'] = $validated['business_description'];
            session(['website_config' => $config]);

            // Force save session
            session()->save();

            Log::info('Step 2 session saved', [
                'config' => $config,
                'business_name' => session('business_name'),
                'website_type' => session('website_type'),
            ]);

            return redirect()->route('website-configurator.step3')
                ->with('success', 'Step 2 completed successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Step 2 validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all(),
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check your input and try again.');
                
        } catch (\Exception $e) {
            Log::error('Step 2 submission error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Generate AI-optimized website description
     * Converts long descriptions to concise, website-ready format (max 500 chars)
     */
    public function generateWebsiteDescription(Request $request)
    {
        $request->validate([
            'original_description' => 'nullable|string',
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string',
            'website_type' => 'required|string',
        ]);

        try {
            $websiteDescription = $this->claudeService->generateWebsiteDescription(
                $request->original_description ?? '',
                $request->business_name,
                $request->business_type,
                $request->website_type
            );

            return response()->json([
                'success' => true,
                'website_description' => $websiteDescription,
                'character_count' => strlen($websiteDescription),
                'message' => 'Website description generated successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Website Description Generation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate description. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Step 3: Choose pages and features
     */
    public function step3View()
    {
        Log::info('Step 3 view accessed', [
            'user_id' => Auth::id(),
            'session_data' => [
                'website_type' => session('website_type'),
                'business_name' => session('business_name'),
                'website_config' => session('website_config'),
            ],
        ]);

        // Ensure previous steps are complete
        if (!session()->has('website_type') || !session()->has('business_name')) {
            Log::warning('Step 3 access denied - missing session data', [
                'has_website_type' => session()->has('website_type'),
                'has_business_name' => session()->has('business_name'),
                'all_session' => session()->all(),
            ]);
            
            return redirect()->route('website-configurator.step1')
                ->with('error', 'Please complete all previous steps first. Session data was missing.');
        }

        // Load available themes
        $themes = WebsiteTheme::where('is_active', true)
            ->where('is_free', true)
            ->orderBy('name')
            ->get();

        Log::info('Step 3 view rendering', ['themes_count' => $themes->count()]);

        return view('website-configurator.step3', compact('themes'));
    }

    /**
     * Step 3: Choose pages and features (submission handler)
     */
    public function step3(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'required|string',
            'pages' => 'nullable|array',
            'pages.*' => 'string',
            'features' => 'nullable|array',
            'features.*' => 'string',
        ]);

        $config = session('website_config', []);
        $config['theme'] = $validated['theme'];
        $config['pages'] = $validated['pages'] ?? ['home', 'contact'];
        $config['features'] = $validated['features'] ?? [];
        session(['website_config' => $config]);

        return redirect()->route('website-configurator.step4');
    }

    /**
     * Show Step 4: Build progress
     */
    public function step4View()
    {
        // Ensure all previous steps are complete
        if (!session()->has('website_config')) {
            return redirect()->route('website-configurator.step1')
                ->with('error', 'Please complete all previous steps first');
        }

        $business = Auth::user()->business;
        $config = session('website_config', []);

        return view('website-configurator.step4', [
            'business' => $business,
            'config' => $config,
        ]);
    }

    /**
     * Build handler - triggers the actual website creation process
     */
    public function build(Request $request)
    {
        // Call the process method to actually create the website
        return $this->process($request);
    }

    /**
     * Process: Actually create the website
     */
    public function process(Request $request)
    {
        try {
            DB::beginTransaction();

            $business = Auth::user()->business;
            
            if (!$business) {
                throw new \Exception('No business found for user');
            }
            
            $config = session('website_config', []);

            // Get or create default theme based on website type
            $theme = $this->getThemeForType($config['website_type'] ?? 'business');

            // Create website
            $website = Website::create([
                'business_id' => $business->id,
                'subdomain' => $business->slug,
                'theme_id' => $theme->id,
                'is_active' => true,
                'is_published' => false,
                'settings' => [
                    'site_name' => $config['business_name'] ?? $business->name,
                    'tagline' => $config['business_description'] ?? $business->description,
                    'contact_email' => $business->email,
                    'contact_phone' => $business->phone,
                    'website_type' => $config['website_type'] ?? 'business',
                ],
                'colors' => $theme->default_colors,
                'fonts' => $theme->default_fonts,
            ]);

            // Initialize website with comprehensive default content
            $this->contentService->initializeWebsiteContent($website);

            // Update business
            $business->update([
                'website_enabled' => true,
                'website_created_at' => now(),
            ]);

            DB::commit();

            // Clear session
            session()->forget('website_config');

            return response()->json([
                'success' => true,
                'message' => 'Website created successfully!',
                'redirect_url' => route('website.builder.index'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Website configuration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create website: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recommended pages based on website type
     */
    protected function getRecommendedPages($websiteType)
    {
        $pages = [
            'business' => [
                ['id' => 'about', 'name' => 'About Us', 'description' => 'Info and stats about your company', 'icon' => '🏢', 'recommended' => true],
                ['id' => 'services', 'name' => 'Services', 'description' => 'Description of your services', 'icon' => '🛠️', 'recommended' => true],
                ['id' => 'contact', 'name' => 'Contact', 'description' => 'Get in touch with your team', 'icon' => '📧', 'recommended' => true],
                ['id' => 'team', 'name' => 'Our Team', 'description' => 'Meet your team members', 'icon' => '👥', 'recommended' => false],
                ['id' => 'testimonials', 'name' => 'Testimonials', 'description' => 'Customer reviews', 'icon' => '⭐', 'recommended' => false],
            ],
            'online_store' => [
                ['id' => 'shop', 'name' => 'Shop', 'description' => 'Sell more with an eCommerce', 'icon' => '🛍️', 'recommended' => true],
                ['id' => 'about', 'name' => 'About', 'description' => 'Your store story', 'icon' => '🏢', 'recommended' => true],
                ['id' => 'contact', 'name' => 'Contact', 'description' => 'Customer support', 'icon' => '📧', 'recommended' => true],
                ['id' => 'shipping', 'name' => 'Shipping Info', 'description' => 'Delivery policies', 'icon' => '📦', 'recommended' => false],
                ['id' => 'returns', 'name' => 'Returns', 'description' => 'Return policy', 'icon' => '↩️', 'recommended' => false],
            ],
            'restaurant' => [
                ['id' => 'menu', 'name' => 'Menu', 'description' => 'Your food and drinks', 'icon' => '🍽️', 'recommended' => true],
                ['id' => 'about', 'name' => 'About', 'description' => 'Your restaurant story', 'icon' => '🏢', 'recommended' => true],
                ['id' => 'reservations', 'name' => 'Reservations', 'description' => 'Table booking', 'icon' => '📅', 'recommended' => true],
                ['id' => 'gallery', 'name' => 'Gallery', 'description' => 'Photos of your dishes', 'icon' => '📸', 'recommended' => false],
                ['id' => 'contact', 'name' => 'Contact', 'description' => 'Location and hours', 'icon' => '📧', 'recommended' => false],
            ],
            'service' => [
                ['id' => 'services', 'name' => 'Services', 'description' => 'What you offer', 'icon' => '🛠️', 'recommended' => true],
                ['id' => 'booking', 'name' => 'Book Now', 'description' => 'Online appointment booking', 'icon' => '📅', 'recommended' => true],
                ['id' => 'pricing', 'name' => 'Pricing', 'description' => 'Service rates', 'icon' => '💰', 'recommended' => true],
                ['id' => 'about', 'name' => 'About', 'description' => 'Your expertise', 'icon' => '🏢', 'recommended' => false],
                ['id' => 'contact', 'name' => 'Contact', 'description' => 'Get in touch', 'icon' => '📧', 'recommended' => false],
            ],
        ];

        return $pages[$websiteType] ?? $pages['business'];
    }

    /**
     * Get recommended features based on website type
     */
    protected function getRecommendedFeatures($websiteType)
    {
        return [
            ['id' => 'contact_form', 'name' => 'Contact Form', 'description' => 'Let visitors reach you easily', 'icon' => '📝'],
            ['id' => 'social_links', 'name' => 'Social Media Links', 'description' => 'Connect your social profiles', 'icon' => '📱'],
            ['id' => 'google_maps', 'name' => 'Google Maps', 'description' => 'Show your location', 'icon' => '🗺️'],
            ['id' => 'testimonials', 'name' => 'Customer Reviews', 'description' => 'Display testimonials', 'icon' => '⭐'],
            ['id' => 'newsletter', 'name' => 'Newsletter Signup', 'description' => 'Collect email subscribers', 'icon' => '📬'],
        ];
    }

    /**
     * Get theme for website type
     */
    protected function getThemeForType($websiteType)
    {
        // Check if theme was selected in step 3
        $config = session('website_config', []);
        if (isset($config['theme'])) {
            $theme = WebsiteTheme::where('slug', $config['theme'])
                ->where('is_active', true)
                ->first();
            
            if ($theme) {
                return $theme;
            }
        }
        
        // Fallback: Try to find a theme for this type
        $theme = WebsiteTheme::where('category', $websiteType)
            ->where('is_active', true)
            ->where('is_free', true)
            ->first();

        if (!$theme) {
            // Get or create default theme
            $theme = WebsiteTheme::where('is_active', true)
                ->where('is_free', true)
                ->first();
        }

        if (!$theme) {
            // Create a default theme if none exists
            $theme = WebsiteTheme::create([
                'name' => 'Default Theme',
                'slug' => 'default',
                'description' => 'Clean and modern default theme',
                'category' => 'business',
                'style' => 'modern',
                'is_free' => true,
                'is_active' => true,
                'default_colors' => [
                    'primary' => '#4F46E5',
                    'secondary' => '#10B981',
                    'accent' => '#F59E0B',
                    'background' => '#FFFFFF',
                    'text' => '#1F2937',
                ],
                'default_fonts' => [
                    'heading' => 'Poppins',
                    'body' => 'Inter',
                ],
            ]);
        }

        return $theme;
    }

    /**
     * Create pages for the website
     */
    protected function createPages(Website $website, array $config)
    {
        $selectedPages = $config['pages'] ?? [];
        $websiteType = $config['website_type'] ?? 'business';

        // Always create homepage
        $homepage = WebsitePage::create([
            'website_id' => $website->id,
            'title' => 'Home',
            'slug' => 'home',
            'is_homepage' => true,
            'is_published' => true,
            'show_in_menu' => true,
            'order' => 0,
        ]);

        // Create hero section for homepage
        $this->createHeroSection($homepage, $config);

        // Create selected pages
        $order = 1;
        foreach ($selectedPages as $pageId) {
            $pageData = $this->getPageData($pageId, $websiteType);
            
            $page = WebsitePage::create([
                'website_id' => $website->id,
                'title' => $pageData['title'],
                'slug' => $pageData['slug'],
                'is_homepage' => false,
                'is_published' => true,
                'show_in_menu' => true,
                'order' => $order++,
            ]);

            // Create sections for this page
            $this->createPageSections($page, $pageId, $config);
        }
    }

    /**
     * Create hero section
     */
    protected function createHeroSection(WebsitePage $page, array $config)
    {
        $businessName = $config['business_name'] ?? $page->website->business->name;
        $description = $config['business_description'] ?? $page->website->business->description;

        WebsiteSection::create([
            'page_id' => $page->id,
            'type' => 'hero',
            'order' => 1,
            'is_visible' => true,
            'content' => [
                'heading' => 'Welcome to ' . $businessName,
                'subheading' => $description ?? 'Your trusted partner for quality services',
                'cta_text' => 'Get Started',
                'cta_link' => '#contact',
            ],
        ]);
    }

    /**
     * Get page data
     */
    protected function getPageData($pageId, $websiteType)
    {
        $pages = [
            'about' => ['title' => 'About Us', 'slug' => 'about'],
            'services' => ['title' => 'Our Services', 'slug' => 'services'],
            'contact' => ['title' => 'Contact Us', 'slug' => 'contact'],
            'shop' => ['title' => 'Shop', 'slug' => 'shop'],
            'menu' => ['title' => 'Menu', 'slug' => 'menu'],
            'team' => ['title' => 'Our Team', 'slug' => 'team'],
            'pricing' => ['title' => 'Pricing', 'slug' => 'pricing'],
            'booking' => ['title' => 'Book Appointment', 'slug' => 'booking'],
            'testimonials' => ['title' => 'Testimonials', 'slug' => 'testimonials'],
            'gallery' => ['title' => 'Gallery', 'slug' => 'gallery'],
        ];

        return $pages[$pageId] ?? ['title' => ucfirst($pageId), 'slug' => $pageId];
    }

    /**
     * Create sections for a page
     */
    protected function createPageSections(WebsitePage $page, string $pageId, array $config)
    {
        // Create basic section for each page type
        $content = $this->getDefaultContent($pageId, $config);

        WebsiteSection::create([
            'page_id' => $page->id,
            'type' => $content['type'],
            'order' => 1,
            'is_visible' => true,
            'content' => $content['data'],
        ]);
    }

    /**
     * Get default content for page types
     */
    protected function getDefaultContent($pageId, $config)
    {
        $businessName = $config['business_name'] ?? '';

        $contents = [
            'about' => [
                'type' => 'about',
                'data' => [
                    'heading' => 'About ' . $businessName,
                    'text' => 'We are committed to providing excellent service to our customers.',
                ],
            ],
            'services' => [
                'type' => 'services',
                'data' => [
                    'heading' => 'Our Services',
                    'services' => [],
                ],
            ],
            'contact' => [
                'type' => 'contact',
                'data' => [
                    'heading' => 'Get In Touch',
                    'show_form' => true,
                ],
            ],
        ];

        return $contents[$pageId] ?? [
            'type' => 'text',
            'data' => ['heading' => ucfirst($pageId)],
        ];
    }

    /**
     * Apply selected features
     */
    protected function applyFeatures(Website $website, array $config)
    {
        $features = $config['features'] ?? [];
        $settings = $website->settings ?? [];

        foreach ($features as $feature) {
            switch ($feature) {
                case 'contact_form':
                    $settings['enable_contact_form'] = true;
                    break;
                case 'social_links':
                    $settings['show_social_links'] = true;
                    break;
                case 'google_maps':
                    $settings['show_google_maps'] = true;
                    break;
                case 'newsletter':
                    $settings['enable_newsletter'] = true;
                    break;
            }
        }

        $website->update(['settings' => $settings]);
    }
}
