<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\WebsitePage;
use App\Models\Product;
use App\Models\Order;
use App\Models\Testimonial;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

            // Determine preview mode:
            // 1. Session flag set by WebsiteBuilderController::preview() (most reliable)
            // 2. Ownership check as fallback (covers direct /site/ visits while logged in)
            $user = $request->user();
            $isPreview = false;
            if ($user) {
                if (session('website_preview_id') == $website->id) {
                    $isPreview = true;
                } else {
                    $isPreview = $user->businesses()->where('id', $website->business_id)->exists()
                              || ($user->business && $user->business->id == $website->business_id);
                }
            }

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
                ->limit(100)
                ->get();

            $orderUrl = $this->resolveOrderUrl($request, $website);

            $businessTestimonials = Testimonial::approved()
                ->forBusiness($website->business_id)
                ->latest()->get();

            $testimonialUrl = $this->resolveTestimonialUrl($request, $website);

            return view('public-website.page', [
                'website'              => $website,
                'page'                 => $page,
                'menuPages'            => $menuPages,
                'products'             => $products,
                'isPreview'            => $isPreview,
                'orderUrl'             => $orderUrl,
                'businessTestimonials' => $businessTestimonials,
                'testimonialUrl'       => $isPreview ? null : $testimonialUrl,
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

            // Determine preview mode (same logic as homepage)
            $user = $request->user();
            $isPreview = false;
            if ($user) {
                if (session('website_preview_id') == $website->id) {
                    $isPreview = true;
                } else {
                    $isPreview = $user->businesses()->where('id', $website->business_id)->exists()
                              || ($user->business && $user->business->id == $website->business_id);
                }
            }

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
                ->limit(100)
                ->get();

            $orderUrl = $this->resolveOrderUrl($request, $website);

            $businessTestimonials = Testimonial::approved()
                ->forBusiness($website->business_id)
                ->latest()->get();

            $testimonialUrl = $this->resolveTestimonialUrl($request, $website);

            return view('public-website.page', [
                'website'              => $website,
                'page'                 => $page,
                'menuPages'            => $menuPages,
                'products'             => $products,
                'isPreview'            => $isPreview,
                'orderUrl'             => $orderUrl,
                'businessTestimonials' => $businessTestimonials,
                'testimonialUrl'       => $isPreview ? null : $testimonialUrl,
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
     * Resolve the correct order POST URL depending on access mode
     * (subdomain vs path-based) to avoid cross-origin fetch issues.
     */
    private function resolveOrderUrl(Request $request, Website $website): string
    {
        $host = $request->getHost();
        $appDomain = env('APP_DOMAIN', 'shopybook.com');
        if (Str::endsWith($host, '.' . $appDomain)) {
            return url('/order');
        }
        return route('public.website.order', $website->subdomain);
    }

    private function resolveTestimonialUrl(Request $request, Website $website): string
    {
        $host = $request->getHost();
        $appDomain = env('APP_DOMAIN', 'shopybook.com');
        if (Str::endsWith($host, '.' . $appDomain)) {
            return url('/testimonial');
        }
        return route('public.website.testimonial', $website->subdomain);
    }

    /**
     * Place a website storefront order (no upfront payment required)
     */
    public function placeOrder(Request $request, $subdomain)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:30',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
            'notes'          => 'nullable|string|max:1000',
            'items'          => 'required|array|min:1|max:50',
            'items.*.id'     => 'required|integer',
            'items.*.qty'    => 'required|integer|min:1|max:999',
        ]);

        $website = Website::where('subdomain', $subdomain)->firstOrFail();

        // Verify every product belongs to this business and re-calculate prices server-side
        $cartItems = [];
        $total = 0;
        foreach ($validated['items'] as $item) {
            $product = Product::where('business_id', $website->business_id)
                ->where('id', $item['id'])
                ->firstOrFail();

            $lineTotal = $product->price * $item['qty'];
            $total += $lineTotal;
            $cartItems[] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => (float) $product->price,
                'qty'   => (int) $item['qty'],
                'total' => $lineTotal,
            ];
        }

        $order = Order::create([
            'business_id'     => $website->business_id,
            'product_id'      => count($cartItems) === 1 ? $cartItems[0]['id'] : null,
            'customer_name'   => $validated['name'],
            'customer_phone'  => $validated['phone'],
            'customer_email'  => $validated['email'] ?? null,
            'delivery_address' => $validated['address'] ?? null,
            'quantity'        => array_sum(array_column($cartItems, 'qty')),
            'unit_price'      => count($cartItems) === 1 ? $cartItems[0]['price'] : null,
            'total_price'     => $total,
            'total_amount'    => $total,
            'order_type'      => 'public_order',
            'payment_status'  => 'unpaid',
            'status'          => 'pending',
            'notes'           => json_encode([
                'cart_items'    => $cartItems,
                'customer_note' => $validated['notes'] ?? null,
            ]),
        ]);

        Log::info('Website storefront order placed', [
            'order_id'    => $order->id,
            'business_id' => $website->business_id,
            'items_count' => count($cartItems),
            'total'       => $total,
        ]);

        try {
            (new NotificationService())->notifyNewOrder($order);
        } catch (\Exception $e) {
            Log::error('Failed to send website order notifications: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully! The business will contact you to arrange delivery and payment.',
            'order_id' => $order->id,
        ]);
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

