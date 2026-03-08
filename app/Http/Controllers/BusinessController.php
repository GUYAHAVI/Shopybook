<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Services\TwoFactorAuthService;
use App\Services\ClaudeAPIService;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    protected $claudeService;

    public function __construct(ClaudeAPIService $claudeService)
    {
        $this->claudeService = $claudeService;
    }
    /**
     * Display a listing of businesses
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'name');
        $order = $request->get('order', 'asc');
        $type = $request->get('type', 'all');

        $query = Business::query();

        if ($type !== 'all') {
            $query->where('business_category', $type);
        }

        $groupedBusinesses = $query->orderBy($sort, $order)
            ->get()
            ->groupBy('business_category');

        return view('businesses', compact('groupedBusinesses', 'sort', 'order', 'type'));
    }

    /**
     * Show the business type selection page
     */
  public function chooseType()
{
    // Use same access method as dashboard
    if (auth()->user()->business()->exists()) {
        return redirect()->route('dashboard');
    }

    return view('business.choose-type');
}

    /**
     * Show the form for creating a new business
     */
    public function create(Request $request)
    {
        // Check if user already has a business
        if (Auth::user()->business) {
            return redirect()->route('dashboard');
        }

        $type = $request->get('type', 'product');
        
        // Map business types to appropriate categories
        $businessTypeOptions = [
            'product' => [
                'retail' => 'Retail Shop',
                'online' => 'Online Store',
                'fashion' => 'Fashion & Clothing',
                'electronics' => 'Electronics',
                'grocery' => 'Grocery Store',
                'beauty' => 'Beauty & Cosmetics',
                'wholesale' => 'Wholesale Business',
                'other_product' => 'Other Product Business',
            ],
            'service' => [
                'consulting' => 'Consulting Services',
                'beauty_service' => 'Beauty & Wellness Services',
                'repair' => 'Repair Services',
                'cleaning' => 'Cleaning Services',
                'education' => 'Education & Training',
                'healthcare' => 'Healthcare Services',
                'professional' => 'Professional Services',
                'other_service' => 'Other Service Business',
            ],
            'hybrid' => [
                'restaurant' => 'Restaurant',
                'salon' => 'Salon & Spa',
                'auto_service' => 'Auto Service Center',
                'retail_service' => 'Retail with Services',
                'tech_service' => 'Technology Sales & Support',
                'other_hybrid' => 'Other Hybrid Business',
            ]
        ];

        return view('business.create', [
            'businessTypes' => $businessTypeOptions[$type] ?? $businessTypeOptions['product'],
            'selectedType' => $type,
            'typeTitle' => ucfirst($type) . ' Business'
        ]);
    }

    /**
     * Store a newly created business
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:businesses',
            'business_type' => 'required|string',
            'description' => 'nullable|string',
            'email' => 'nullable|email|unique:businesses',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'required|string',
            'terms' => 'required|accepted',
            'logo' => 'nullable|image|max:2048',
            'generated_logo_path' => 'nullable|string',
        ]);

        try {
            // Determine business category based on business type
            $businessCategory = $this->getBusinessCategory($validated['business_type']);
            
            // Generate unique slug
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            
            // Ensure slug is unique
            while (Business::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Create the business
            $business = Business::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'slug' => $slug,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'],
                'business_type' => $validated['business_type'],
                'business_category' => $businessCategory,
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'],
                'country' => 'Kenya',
            ]);

            // Start 2-week Enterprise trial for new businesses
            $business->startTrial(14, 'enterprise');

            Log::info('Business created with trial', [
                'business_id' => $business->id,
                'trial_ends_at' => $business->trial_ends_at,
                'plan' => $business->plan
            ]);

            // Handle logo - prioritize uploaded logo over AI-generated
            if ($request->hasFile('logo')) {
                // User uploaded a logo file
                $path = $request->file('logo')->store('business/logos', 'public');
                $business->update(['logo_path' => $path]);
                
                Log::info('Business logo uploaded', [
                    'business_id' => $business->id,
                    'logo_path' => $path
                ]);
            } elseif ($request->filled('generated_logo_path')) {
                // User generated a logo with AI
                $business->update(['logo_path' => $validated['generated_logo_path']]);
                
                Log::info('Business AI-generated logo saved', [
                    'business_id' => $business->id,
                    'logo_path' => $validated['generated_logo_path']
                ]);
            }

            Log::info('Business created successfully', [
                'business_id' => $business->id,
                'user_id' => Auth::id(),
                'business_name' => $business->name
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Business created successfully! Welcome to Shopybook.');

        } catch (\Exception $e) {
            Log::error('Failed to create business', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to create business. Please try again.');
        }
    }

    /**
     * Show the form for editing the business
     */
    public function edit()
    {
        $business = Auth::user()->business;
        
        if (!$business) {
            return redirect()->route('business.create');
        }

        // Get business types for the dropdown
        $businessTypeOptions = [
            'product' => [
                'retail' => 'Retail Store',
                'wholesale' => 'Wholesale Business',
                'manufacturing' => 'Manufacturing',
                'agriculture' => 'Agriculture & Farming',
                'construction' => 'Construction & Building',
                'other_product' => 'Other Product Business',
            ],
            'service' => [
                'consulting' => 'Consulting Services',
                'healthcare' => 'Healthcare Services',
                'education' => 'Education & Training',
                'financial' => 'Financial Services',
                'legal' => 'Legal Services',
                'professional' => 'Professional Services',
                'other_service' => 'Other Service Business',
            ],
            'hybrid' => [
                'restaurant' => 'Restaurant',
                'salon' => 'Salon & Spa',
                'auto_service' => 'Auto Service Center',
                'retail_service' => 'Retail with Services',
                'tech_service' => 'Technology Sales & Support',
                'other_hybrid' => 'Other Hybrid Business',
            ]
        ];

        // Determine which category the current business type belongs to
        $currentCategory = null;
        foreach ($businessTypeOptions as $category => $types) {
            if (array_key_exists($business->business_type, $types)) {
                $currentCategory = $category;
                break;
            }
        }

        $businessTypes = $currentCategory ? $businessTypeOptions[$currentCategory] : $businessTypeOptions['product'];

        return view('business.edit', compact('business', 'businessTypes'));
    }

    /**
     * Update the specified business
     */
    public function update(Request $request, TwoFactorAuthService $twoFactorService)
    {
        $business = Auth::user()->business;
        
        if (!$business) {
            return redirect()->route('business.create');
        }

        // Check if 2FA verification is required and completed
        if (!session('2fa_verified_business_edit')) {
            // Send 2FA code and redirect to verification
            $context = [
                'business_id' => $business->id,
                'business_name' => $business->name,
                'request_data' => $request->all()
            ];
            
            $twoFactorService->sendVerificationCode(Auth::user(), 'business_edit', $context);
            
            return redirect()->route('2fa.verify.form', [
                'action' => 'business_edit',
                'context' => $context
            ])->with('info', 'Please verify your identity to edit your business profile.');
        }

        // Clear the 2FA session
        session()->forget('2fa_verified_business_edit');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:businesses,name,' . $business->id,
            'business_type' => 'required|string',
            'description' => 'nullable|string',
            'email' => 'nullable|email|unique:businesses,email,' . $business->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'generated_logo_path' => 'nullable|string',
        ]);

        try {
            // Determine business category based on business type
            $businessCategory = $this->getBusinessCategory($validated['business_type']);
            
            // Generate unique slug only if name has changed
            $slug = $business->slug; // Keep existing slug by default
            if ($business->name !== $validated['name']) {
                $baseSlug = Str::slug($validated['name']);
                $slug = $baseSlug;
                $counter = 1;
                
                // Ensure slug is unique (exclude current business from check)
                while (Business::where('slug', $slug)->where('id', '!=', $business->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
            }
            
            $business->update([
                'name' => $validated['name'],
                'slug' => $slug,
                'business_type' => $validated['business_type'],
                'business_category' => $businessCategory,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'],
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'],
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($business->logo_path) {
                    Storage::disk('public')->delete($business->logo_path);
                }
                $path = $request->file('logo')->store('business/logos', 'public');
                $business->update(['logo_path' => $path]);
            } elseif ($request->filled('generated_logo_path')) {
                // Handle AI-generated logo
                $business->update(['logo_path' => $validated['generated_logo_path']]);
            }

            Log::info('Business updated successfully with 2FA', [
                'business_id' => $business->id,
                'user_id' => Auth::id(),
                'changes' => $validated
            ]);

            return redirect()->route('business.edit')
                ->with('success', 'Business updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update business', [
                'business_id' => $business->id,
                'error' => $e->getMessage()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update business. Please try again.');
        }
    }

    /**
     * Send email verification code for business deletion
     */
    public function sendDeletionCode(Request $request, TwoFactorAuthService $twoFactorService)
    {
        try {
            $business = Auth::user()->business;
            
            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'No business found.'
                ], 404);
            }

            // Send email verification code
            $context = [
                'business_id' => $business->id,
                'business_name' => $business->name,
                'action' => 'delete'
            ];
            
            $sent = $twoFactorService->sendVerificationCode(Auth::user(), 'business_delete', $context);
            
            if ($sent) {
                Log::info('Business deletion code sent', [
                    'business_id' => $business->id,
                    'user_id' => Auth::id()
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Verification code sent to your email address.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification code. Please try again.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send deletion code', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify deletion code and delete business
     */
    public function verifyAndDelete(Request $request, TwoFactorAuthService $twoFactorService)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        try {
            $business = Auth::user()->business;
            
            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'No business found.'
                ], 404);
            }

            // Verify the code
            $result = $twoFactorService->verifyCode(Auth::user(), 'business_delete', $request->code);
            
            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

            // Code verified, proceed with deletion
            $businessName = $business->name;
            $businessId = $business->id;
            
            // Delete business logo if exists
            if ($business->logo_path) {
                Storage::disk('public')->delete($business->logo_path);
            }
            
            // Delete business cover if exists
            if ($business->cover_path) {
                Storage::disk('public')->delete($business->cover_path);
            }
            
            $business->delete();
            
            // Refresh the user model to clear the cached business relationship
            Auth::user()->refresh();
            
            // Clear any session data related to the business
            session()->forget(['business_id', 'business_name']);

            Log::info('Business deleted successfully with email verification', [
                'business_id' => $businessId,
                'user_id' => Auth::id(),
                'business_name' => $businessName,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Business deleted successfully.',
                'redirect' => route('business.choose-type')
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete business', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete business. Please try again.'
            ], 500);
        }
    }

    /**
     * Initiate business deletion with 2FA (Legacy method - kept for backward compatibility)
     */
    public function initiateDeletion(Request $request, TwoFactorAuthService $twoFactorService)
    {
        $business = Auth::user()->business;
        
        if (!$business) {
            return redirect()->route('business.create');
        }

        // Send 2FA code for business deletion
        $context = [
            'business_id' => $business->id,
            'business_name' => $business->name,
            'action' => 'delete'
        ];
        
        $twoFactorService->sendVerificationCode(Auth::user(), 'business_delete', $context);
        
        return redirect()->route('2fa.verify.form', [
            'action' => 'business_delete',
            'context' => $context
        ])->with('warning', 'Please verify your identity to delete your business. This action cannot be undone.');
    }

    /**
     * Remove the specified business (after 2FA verification - Legacy method)
     */
    public function destroy(Business $business, Request $request, TwoFactorAuthService $twoFactorService)
    {
        // Verify the business belongs to the authenticated user
        if ($business->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if 2FA verification is completed
        if (!session('2fa_verified_business_delete')) {
            return redirect()->route('business.edit')
                ->with('error', 'Security verification required to delete business.');
        }

        // Clear the 2FA session
        session()->forget('2fa_verified_business_delete');

        try {
            $businessName = $business->name;
            $businessId = $business->id;
            
            // Delete business logo if exists
            if ($business->logo_path) {
                Storage::disk('public')->delete($business->logo_path);
            }
            
            // Delete business cover if exists
            if ($business->cover_path) {
                Storage::disk('public')->delete($business->cover_path);
            }
            
            $business->delete();
            
            // Refresh the user model to clear the cached business relationship
            Auth::user()->refresh();
            
            // Clear any session data related to the business
            session()->forget(['business_id', 'business_name']);

            Log::info('Business deleted successfully with 2FA', [
                'business_id' => $businessId,
                'user_id' => Auth::id(),
                'business_name' => $businessName,
                'ip' => $request->ip()
            ]);

            return redirect()->route('business.choose-type')
                ->with('success', 'Business "' . $businessName . '" has been deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete business', [
                'business_id' => $business->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to delete business. Please try again.');
        }
    }

    /**
     * Display the specified business (Public - No authentication required)
     */
    public function show($slug)
    {
        // Add debug logging to see what's happening
        Log::info('BusinessController@show called', [
            'slug' => $slug,
            'authenticated' => auth()->check(),
            'user_id' => auth()->check() ? auth()->id() : null,
        ]);

        // Find business by slug with active status
        $business = Business::where('slug', $slug)
                          ->where('active', true)
                          ->first();
        
        // Add debug logging for business search
        Log::info('Business search result', [
            'slug' => $slug,
            'business_found' => $business ? true : false,
            'business_id' => $business ? $business->id : null,
            'business_name' => $business ? $business->name : null,
        ]);
        
        if (!$business) {
            Log::warning('Business not found', ['slug' => $slug]);
            return redirect()->route('businesses')->with('error', 'Business not found or is not currently active.');
        }
        
        // Get services and products for this business
        $services = $business->services()->get() ?? collect();
        $products = $business->products()->get() ?? collect();
        
        return view('business.show', compact('business', 'services', 'products'));
    }

    /**
     * Determine business category based on business type
     */
    private function getBusinessCategory($businessType)
    {
        $productTypes = ['retail', 'online', 'fashion', 'electronics', 'grocery', 'beauty', 'wholesale', 'other_product'];
        $serviceTypes = ['consulting', 'beauty_service', 'repair', 'cleaning', 'education', 'healthcare', 'professional', 'other_service'];
        $hybridTypes = ['restaurant', 'salon', 'auto_service', 'retail_service', 'tech_service', 'other_hybrid'];

        if (in_array($businessType, $productTypes)) {
            return 'product';
        } elseif (in_array($businessType, $serviceTypes)) {
            return 'service';
        } elseif (in_array($businessType, $hybridTypes)) {
            return 'hybrid';
        }

        return 'other';
    }

    /**
     * Enhance business description using Claude AI
     */
    public function enhanceDescription(Request $request)
    {
        $request->validate([
            'description' => 'required|string|min:10',
            'business_name' => 'required|string',
            'business_type' => 'required|string',
        ]);

        try {
            $enhancedDescription = $this->claudeService->enhanceBusinessDescription(
                $request->description,
                $request->business_name,
                $request->business_type
            );

            return response()->json([
                'success' => true,
                'enhanced_description' => $enhancedDescription,
                'original_description' => $request->description
            ]);

        } catch (\Exception $e) {
            Log::error('Description Enhancement Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to enhance description. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate business logo using AI
     */
    public function generateLogo(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_description' => 'nullable|string',
            'business_type' => 'required|string',
            'logo_style' => 'nullable|string|in:modern,classic,minimal,bold,playful,corporate',
            'tagline' => 'nullable|string|max:50',
        ]);

        try {
            $style = $request->logo_style ?? 'modern';
            $tagline = $request->tagline;
            
            // Ensure description meets minimum requirements
            $description = $request->business_description;
            if (empty($description) || strlen($description) < 10) {
                $description = 'A professional ' . $request->business_type . ' business providing quality services and products to our valued customers.';
            }
            
            Log::info('Logo generation requested', [
                'business_name' => $request->business_name,
                'business_type' => $request->business_type,
                'style' => $style,
                'has_tagline' => !empty($tagline)
            ]);

            $logoResult = $this->claudeService->generateBusinessLogo(
                $request->business_name,
                $description,
                $request->business_type,
                $style,
                $tagline
            );

            if ($logoResult && isset($logoResult['public_url'])) {
                Log::info('Logo generation successful', [
                    'business_name' => $request->business_name,
                    'style' => $style,
                    'logo_url' => $logoResult['public_url'],
                    'has_tagline' => !empty($tagline)
                ]);
                
                return response()->json([
                    'success' => true,
                    'logo_url' => $logoResult['public_url'],
                    'logo_path' => $logoResult['local_path'],
                    'message' => 'Logo generated successfully with business name and tagline!'
                ]);
            }

            Log::error('Logo generation returned null or invalid result', [
                'business_name' => $request->business_name,
                'business_type' => $request->business_type,
                'style' => $style,
                'result' => $logoResult
            ]);
            
            throw new \Exception('Failed to generate logo - all methods returned null');

        } catch (\Exception $e) {
            Log::error('Logo Generation Error: ' . $e->getMessage(), [
                'business_name' => $request->business_name,
                'business_type' => $request->business_type,
                'style' => $style ?? 'unknown',
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate logo. Please try again or upload your own logo.',
                'error' => $e->getMessage(),
                'debug_info' => app()->environment('local') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }
}

