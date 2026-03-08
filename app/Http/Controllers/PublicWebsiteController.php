<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\WebsitePage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicWebsiteController extends Controller
{
    /**
     * Show website homepage
     */
    public function homepage(Request $request, $subdomain)
    {
        try {
            $website = Website::where('subdomain', $subdomain)
                ->with(['business', 'theme'])
                ->firstOrFail();

            // Check if preview mode - allow authenticated users to preview even if not published
            $isPreview = $request->user() && $request->user()->businesses()->where('id', $website->business_id)->exists();

            // Check if website is accessible (skip check for authenticated owners)
            if (!$isPreview && !$website->isAccessible()) {
                abort(404, 'Website not found or not published');
            }

            // Get homepage - include all sections for preview mode
            $pageQuery = $website->homepage()->with(['sections' => function($query) use ($isPreview) {
                if (!$isPreview) {
                    $query->where('is_visible', true);
                }
                $query->orderBy('order');
            }]);
            
            $page = $pageQuery->firstOrFail();

            // Increment views (only if not preview)
            if (!$isPreview) {
                $website->incrementViews();
                $website->incrementVisits();
                $page->incrementViews();
            }

            // Get menu pages
            $menuPages = $website->menuPages()->get();

            // Get products for product sections
            $products = $website->business->products()
                ->where('stock_quantity', '>', 0)
                ->limit(12)
                ->get();

            return view('public-website.page', [
                'website' => $website,
                'page' => $page,
                'menuPages' => $menuPages,
                'products' => $products,
                'isPreview' => $isPreview,
            ]);

        } catch (\Exception $e) {
            Log::error('Public website error', [
                'subdomain' => $subdomain,
                'error' => $e->getMessage(),
            ]);
            abort(404);
        }
    }

    /**
     * Show website page
     */
    public function page(Request $request, $subdomain, $slug)
    {
        try {
            $website = Website::where('subdomain', $subdomain)
                ->with(['business', 'theme'])
                ->firstOrFail();

            // Check if preview mode - allow authenticated users to preview even if not published
            $isPreview = $request->user() && $request->user()->businesses()->where('id', $website->business_id)->exists();

            // Check if website is accessible (skip check for authenticated owners)
            if (!$isPreview && !$website->isAccessible()) {
                abort(404, 'Website not found or not published');
            }

            // Get page - include all sections for preview mode
            $pageQuery = $website->pages()
                ->where('slug', $slug)
                ->with(['sections' => function($query) use ($isPreview) {
                    if (!$isPreview) {
                        $query->where('is_visible', true);
                    }
                    $query->orderBy('order');
                }]);
            
            $page = $pageQuery->firstOrFail();

            // Check if page is published (skip check for authenticated owners)
            if (!$isPreview && !$page->is_published) {
                abort(404, 'Page not found');
            }

            // Increment views (only if not preview)
            if (!$isPreview) {
                $website->incrementViews();
                $page->incrementViews();
            }

            // Get menu pages
            $menuPages = $website->menuPages()->get();

            // Get products for product sections
            $products = $website->business->products()
                ->where('stock_quantity', '>', 0)
                ->limit(12)
                ->get();

            return view('public-website.page', [
                'website' => $website,
                'page' => $page,
                'menuPages' => $menuPages,
                'products' => $products,
                'isPreview' => $isPreview,
            ]);

        } catch (\Exception $e) {
            Log::error('Public website page error', [
                'subdomain' => $subdomain,
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);
            abort(404);
        }
    }

    /**
     * Handle contact form submission
     */
    public function submitContact(Request $request, $subdomain)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        $website = Website::where('subdomain', $subdomain)->firstOrFail();

        // Log contact submission
        Log::info('Contact form submission', [
            'website_id' => $website->id,
            'business_id' => $website->business_id,
            'contact_data' => $validated,
        ]);

        // TODO: Send email notification to business owner
        // TODO: Save to database if you create a contacts table

        return back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }

    /**
     * Get product details (AJAX)
     */
    public function getProduct($subdomain, $productId)
    {
        $website = Website::where('subdomain', $subdomain)->firstOrFail();
        
        $product = Product::where('business_id', $website->business_id)
            ->where('id', $productId)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }
}

