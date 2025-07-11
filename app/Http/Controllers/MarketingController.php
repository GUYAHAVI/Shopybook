<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

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
}
