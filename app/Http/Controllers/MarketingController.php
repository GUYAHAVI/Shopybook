<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ContactGroup;
use App\Models\ImportedContact;
use App\Services\LTXVideoService;
use App\Services\CloudLTXVideoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\HostPinnacleSmsService;
use Illuminate\Support\Facades\Log;

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
        try {
            Log::info('Bulk SMS page accessed', [
                'user_id' => auth()->id(),
                'timestamp' => now(),
            ]);

            $business = auth()->user()->business;
            
            if (!$business) {
                Log::warning('Bulk SMS access without business', [
                    'user_id' => auth()->id(),
                ]);
                return redirect()->route('dashboard')->with('error', 'Please create a business first.');
            }
            
            $businessId = $business->id;
            
            Log::info('Loading bulk SMS data', [
                'business_id' => $businessId,
                'business_name' => $business->name,
            ]);
            
            $customers = $business->customers()->get();
            
            // Get contact groups
            $contactGroups = \App\Models\ContactGroup::where('business_id', $businessId)
                ->withCount('contacts')
                ->get();
            
            Log::info('Bulk SMS data loaded', [
                'customers_count' => $customers->count(),
                'contact_groups_count' => $contactGroups->count(),
            ]);
            
            $templates = [
                'welcome' => 'Welcome to {{business_name}}! Thank you for choosing us.',
                'promotion' => '🎉 Special offer at {{business_name}}! {{promotion_details}}. Valid until {{end_date}}.',
                'reminder' => 'Hi {{customer_name}}, don\'t forget your appointment at {{business_name}} on {{date}}.',
                'custom' => 'Custom message...'
            ];
            
            return view('marketing.bulk-sms', compact('customers', 'templates', 'contactGroups'));
            
        } catch (\Exception $e) {
            Log::error('Error loading bulk SMS page', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('dashboard')->with('error', 'Error loading bulk SMS page. Please try again or contact support.');
        }
    }

    public function sendBulkSms(Request $request)
    {
        try {
            Log::info('SMS send request initiated', [
                'user_id' => auth()->id(),
                'recipient_type' => $request->recipient_type,
                'message_length' => strlen($request->message ?? ''),
                'scheduled' => !empty($request->scheduled_at),
            ]);

            $request->validate([
                'message' => 'required|string|max:160',
                'recipient_type' => 'required|in:customers,contact_groups',
                'customer_ids' => 'required_if:recipient_type,customers|array',
                'customer_ids.*' => 'exists:customers,id',
                'contact_group_ids' => 'required_if:recipient_type,contact_groups|array',
                'contact_group_ids.*' => 'exists:contact_groups,id',
                'scheduled_at' => 'nullable|date|after:now',
            ]);

            $business = auth()->user()->business;
            
            if (!$business) {
                Log::error('SMS send failed - no business', [
                    'user_id' => auth()->id(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Business not found. Please create a business first.',
                ], 400);
            }

            Log::info('Processing SMS recipients', [
                'business_id' => $business->id,
                'business_name' => $business->name,
            ]);

            $phones = [];
            $recipientCount = 0;
            $businessId = $business->id;
        
            // Collect phone numbers based on recipient type
            if ($request->recipient_type === 'customers') {
                $customers = Customer::whereIn('id', $request->customer_ids)->get();
                $phones = $customers->pluck('phone')->filter()->toArray();
                $recipientCount = count($customers);
                
                Log::info('SMS recipients collected from customers', [
                    'customer_count' => $recipientCount,
                    'valid_phones' => count($phones),
                    'customer_ids' => $request->customer_ids,
                ]);
            } elseif ($request->recipient_type === 'contact_groups') {
                $contactGroups = \App\Models\ContactGroup::where('business_id', $businessId)
                    ->whereIn('id', $request->contact_group_ids)
                    ->with('contacts')
                    ->get();
                
                Log::info('Contact groups loaded', [
                    'group_count' => $contactGroups->count(),
                    'group_ids' => $request->contact_group_ids,
                ]);
                
                foreach ($contactGroups as $group) {
                    $groupPhones = $group->getPhoneNumbers();
                    $phones = array_merge($phones, $groupPhones);
                    
                    Log::debug('Group phones collected', [
                        'group_id' => $group->id,
                        'group_name' => $group->name,
                        'phones_count' => count($groupPhones),
                    ]);
                }
                
                // Remove duplicates
                $phones = array_unique($phones);
                $recipientCount = count($phones);
                
                Log::info('SMS recipients collected from groups', [
                    'total_phones' => $recipientCount,
                    'unique_phones' => count($phones),
                ]);
            }
            
            $smsService = new HostPinnacleSmsService();
            
            // Check if SMS service is configured
            if (!$smsService->isConfigured()) {
                Log::error('SMS service not configured', [
                    'user_id' => auth()->id(),
                    'business_id' => $businessId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'SMS service is not configured. Please contact administrator.',
                ], 500);
            }
            
            if (empty($phones)) {
                Log::warning('No valid phone numbers found', [
                    'recipient_type' => $request->recipient_type,
                    'recipient_count' => $recipientCount,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No valid phone numbers found in selected recipients',
                ], 400);
            }
            
            // Prepare SMS options
            $options = [];
            
            // If scheduled, add schedule time to options
            if ($request->scheduled_at) {
                // Convert to Host Pinnacle format: YYYY-MM-DD HH:MM
                $scheduleTime = \Carbon\Carbon::parse($request->scheduled_at)->format('Y-m-d H:i');
                $options['scheduleTime'] = $scheduleTime;
                
                Log::info('SMS scheduled for future delivery', [
                    'recipient_count' => $recipientCount,
                    'phone_count' => count($phones),
                    'message_length' => strlen($request->message),
                    'scheduled_at' => $scheduleTime,
                    'recipient_type' => $request->recipient_type,
                    'business_id' => $businessId,
                ]);
            }
            
            Log::info('Sending SMS via Host Pinnacle API', [
                'phone_count' => count($phones),
                'message_length' => strlen($request->message),
                'scheduled' => !empty($options['scheduleTime']),
                'business_id' => $businessId,
                'user_id' => auth()->id(),
            ]);
            
            // Send SMS (immediately or scheduled)
            $result = $smsService->sendBulkSms($phones, $request->message, $options);
            
            if ($result['success']) {
                $message = $request->scheduled_at 
                    ? 'SMS scheduled successfully for ' . $result['recipients'] . ' recipients at ' . $scheduleTime
                    : 'SMS sent successfully to ' . $result['recipients'] . ' recipients';
                
                Log::info('SMS sent successfully', [
                    'recipients' => $result['recipients'],
                    'cost_estimate' => $result['cost_estimate'] ?? null,
                    'scheduled' => !empty($request->scheduled_at),
                    'business_id' => $businessId,
                    'user_id' => auth()->id(),
                    'api_response' => $result['data'] ?? null,
                ]);
                    
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'note' => 'Check your SMS provider dashboard for delivery status.',
                    'data' => $result['data'] ?? null,
                    'cost_estimate' => $result['cost_estimate'] ?? null
                ]);
            }
            
            Log::error('SMS sending failed', [
                'error_message' => $result['message'],
                'error_details' => $result['error'] ?? null,
                'phone_count' => count($phones),
                'business_id' => $businessId,
                'user_id' => auth()->id(),
                'api_response' => $result['data'] ?? null,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], 500);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Let validation exceptions pass through
            throw $e;
        } catch (\Exception $e) {
            Log::error('SMS sending exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'business_id' => $business->id ?? null,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending SMS. Please try again or contact support.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
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
