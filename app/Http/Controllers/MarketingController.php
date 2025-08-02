<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\Customer;
use App\Models\Product;
use App\Services\LTXVideoService;
use App\Services\CloudLTXVideoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MarketingController extends Controller
{
    public function promotions()
    {
        $promotions = auth()->user()->business->promotions()
            ->with(['products', 'customers'])
            ->latest()
            ->paginate(15);
            
        return view('marketing.promotions', compact('promotions'));
    }

    public function createPromotion()
    {
        $products = auth()->user()->business->products()->active()->get();
        $customers = auth()->user()->business->customers()->get();
        
        return view('marketing.create-promotion', compact('products', 'customers'));
    }

    public function storePromotion(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        $promotion = new Promotion($request->all());
        $promotion->business_id = auth()->user()->business->id;
        $promotion->code = strtoupper(uniqid());
        $promotion->save();

        if ($request->has('product_ids')) {
            $promotion->products()->attach($request->product_ids);
        }

        if ($request->has('customer_ids')) {
            $promotion->customers()->attach($request->customer_ids);
        }

        return redirect()->route('marketing.promotions')->with('success', 'Promotion created successfully!');
    }

    public function editPromotion(Promotion $promotion)
    {
        $this->authorize('update', $promotion);
        
        $products = auth()->user()->business->products()->active()->get();
        $customers = auth()->user()->business->customers()->get();
        
        return view('marketing.edit-promotion', compact('promotion', 'products', 'customers'));
    }

    public function updatePromotion(Request $request, Promotion $promotion)
    {
        $this->authorize('update', $promotion);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        $promotion->fill($request->all());
        $promotion->save();

        $promotion->products()->sync($request->product_ids ?? []);
        $promotion->customers()->sync($request->customer_ids ?? []);

        return redirect()->route('marketing.promotions')->with('success', 'Promotion updated successfully!');
    }

    public function destroyPromotion(Promotion $promotion)
    {
        $this->authorize('delete', $promotion);
        
        $promotion->delete();
        
        return redirect()->route('marketing.promotions')->with('success', 'Promotion deleted successfully!');
    }

    public function bulkSms()
    {
        $customers = auth()->user()->business->customers()->get();
        $templates = [
            'welcome' => 'Welcome to {{business_name}}! Thank you for choosing us.',
            'promotion' => '🎉 Special offer at {{business_name}}! {{promotion_details}}. Valid until {{end_date}}.',
            'reminder' => 'Hi {{customer_name}}, don\'t forget your appointment at {{business_name}} on {{date}}.',
            'custom' => 'Custom message...'
        ];
        
        return view('marketing.bulk-sms', compact('customers', 'templates'));
    }

    public function sendBulkSms(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:160',
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        // For now, just log the SMS (API integration will be added later)
        $customers = Customer::whereIn('id', $request->customer_ids)->get();
        
        foreach ($customers as $customer) {
            // Log SMS for later API integration
            \Log::info('SMS to be sent', [
                'customer' => $customer->name,
                'phone' => $customer->phone,
                'message' => $request->message,
                'scheduled_at' => $request->scheduled_at
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'SMS queued for sending to ' . count($customers) . ' customers',
            'note' => 'SMS API integration pending. Messages logged for later processing.'
        ]);
    }

    public function advertising()
    {
        $campaigns = auth()->user()->business->advertisingCampaigns()
            ->latest()
            ->paginate(15);
            
        return view('marketing.advertising', compact('campaigns'));
    }

    public function createCampaign()
    {
        return view('marketing.create-campaign');
    }

    public function storeCampaign(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'platform' => 'required|in:facebook,instagram,google,email,sms',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'target_audience' => 'nullable|string',
            'status' => 'required|in:draft,active,paused,completed',
        ]);

        $campaign = new \App\Models\AdvertisingCampaign($request->all());
        $campaign->business_id = auth()->user()->business->id;
        $campaign->save();

        return redirect()->route('marketing.advertising')->with('success', 'Campaign created successfully!');
    }

    public function marketingReport()
    {
        $period = request('period', '30');
        $startDate = now()->subDays($period);
        
        // Promotion usage statistics
        $promotionStats = auth()->user()->business->promotions()
            ->withCount('usage')
            ->orderByDesc('usage_count')
            ->limit(10)
            ->get();
            
        // Customer engagement
        $customerEngagement = auth()->user()->business->customers()
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->limit(10)
            ->get();
            
        // SMS statistics (placeholder for API integration)
        $smsStats = [
            'total_sent' => 0,
            'delivered' => 0,
            'failed' => 0,
            'pending' => 0
        ];
        
        return view('marketing.report', compact('promotionStats', 'customerEngagement', 'smsStats', 'period'));
    }

    public function emailMarketing()
    {
        $customers = auth()->user()->business->customers()->get();
        $templates = [
            'newsletter' => 'Newsletter template',
            'promotion' => 'Promotional email template',
            'welcome' => 'Welcome email template',
            'custom' => 'Custom email template'
        ];
        
        return view('marketing.email', compact('customers', 'templates'));
    }

    public function sendBulkEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
            'template' => 'nullable|string',
        ]);

        $customers = Customer::whereIn('id', $request->customer_ids)->get();
        $sentCount = 0;
        $failedCount = 0;
        
        foreach ($customers as $customer) {
            if ($customer->email) {
                try {
                    // Send email using Laravel's Mail facade
                    \Mail::raw($request->message, function ($message) use ($customer, $request) {
                        $message->to($customer->email, $customer->name)
                                ->subject($request->subject)
                                ->from(config('mail.from.address'), config('mail.from.name'));
                    });
                    
                    $sentCount++;
                    
                    \Log::info('Email sent successfully', [
                        'customer' => $customer->name,
                        'email' => $customer->email,
                        'subject' => $request->subject
                    ]);
                } catch (\Exception $e) {
                    $failedCount++;
                    
                    \Log::error('Email sending failed', [
                        'customer' => $customer->name,
                        'email' => $customer->email,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Emails sent successfully! {$sentCount} delivered, {$failedCount} failed",
            'sent_count' => $sentCount,
            'failed_count' => $failedCount
        ]);
    }

    /**
     * Generate marketing video from post content
     */
    public function generateVideo(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'title' => 'required|string|max:255',
            'style' => 'nullable|string|in:professional,dynamic,minimal,creative,social',
            'product_image' => 'nullable|image|max:10240', // 10MB max
            'duration' => 'nullable|integer|min:3|max:30', // 3-30 seconds
        ]);

        try {
            $business = auth()->user()->business;
            $ltxVideoService = new CloudLTXVideoService();
            
            // Prepare business branding info
            $businessBranding = [
                'name' => $business->name,
                'type' => $business->business_type ?? 'retail',
                'colors' => [
                    $business->primary_color ?? '#007bff',
                    $business->secondary_color ?? '#ffffff'
                ],
                'logo' => $business->logo_path ?? null
            ];
            
            // Handle product image if provided
            $productImagePath = null;
            if ($request->hasFile('product_image')) {
                $productImagePath = $request->file('product_image')->store('temp_images', 'public');
                $productImagePath = storage_path('app/public/' . $productImagePath);
            }
            
            // Generation options
            $options = [
                'style' => $request->get('style', 'professional'),
                'num_frames' => ($request->get('duration', 4) * 30) + 1, // Convert seconds to frames + 1
            ];
            
            // Generate the video
            $result = $ltxVideoService->generateMarketingVideo(
                $request->input('content'),
                $businessBranding,
                $productImagePath,
                $options
            );
            
            // Clean up temp image
            if ($productImagePath && file_exists($productImagePath)) {
                unlink($productImagePath);
            }
            
            if ($result['success']) {
                // Log successful generation for debugging
                \Log::info('Video generation successful', [
                    'result' => $result,
                    'video_url' => $result['public_url'],
                    'file_exists' => file_exists($result['file_path'] ?? '')
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Video generated successfully!',
                    'video_id' => $result['video_id'],
                    'video_url' => $result['public_url'],
                    'duration' => $result['duration'],
                    'generation_time' => $result['generation_time'] ?? null,
                    'prompt_used' => $result['prompt_used'],
                    'debug_info' => $result['debug_info'] ?? null
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Video generation failed: ' . $result['error']
                ], 422);
            }
            
        } catch (\Exception $e) {
            \Log::error('Video generation request failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'business_id' => auth()->user()->business->id ?? null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating the video. Please try again.'
            ], 500);
        }
    }

    /**
     * Get available video styles
     */
    public function getVideoStyles()
    {
        $ltxVideoService = new LTXVideoService();
        return response()->json([
            'styles' => $ltxVideoService->getAvailableStyles()
        ]);
    }

    /**
     * Preview video generation prompt
     */
    public function previewVideoPrompt(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'style' => 'nullable|string|in:professional,dynamic,minimal,creative,social',
        ]);

        try {
            $business = auth()->user()->business;
            $ltxVideoService = new LTXVideoService();
            
            // Prepare business branding info
            $businessBranding = [
                'name' => $business->name,
                'type' => $business->business_type ?? 'retail',
                'colors' => [
                    $business->primary_color ?? '#007bff',
                    $business->secondary_color ?? '#ffffff'
                ]
            ];
            
            $options = [
                'style' => $request->get('style', 'professional')
            ];
            
            // Use reflection to access the private method for preview
            $reflection = new \ReflectionClass($ltxVideoService);
            $method = $reflection->getMethod('createEnhancedPrompt');
            $method->setAccessible(true);
            
            $prompt = $method->invoke($ltxVideoService, $request->input('content'), $businessBranding, $options);
            
            return response()->json([
                'success' => true,
                'prompt' => $prompt,
                'business_name' => $business->name,
                'style' => $options['style']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate prompt preview'
            ], 500);
        }
    }

    /**
     * Cleanup old generated videos
     */
    public function cleanupVideos()
    {
        try {
            $ltxVideoService = new LTXVideoService();
            $deletedCount = $ltxVideoService->cleanupOldVideos();
            
            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} old video files",
                'deleted_count' => $deletedCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup videos: ' . $e->getMessage()
            ], 500);
        }
    }
}
