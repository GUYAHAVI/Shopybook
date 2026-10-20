<?php

namespace App\Http\Controllers;

use App\Models\BusinessSettings;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Display all settings in tabbed interface
     */
    public function index()
    {
        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();
        $taxSettings = $business->getTaxSettingsOrCreate();
        
        // Get available timezones
        $timezones = timezone_identifiers_list();
        
        // Get available languages
        $languages = [
            'en' => 'English',
            'sw' => 'Swahili',
            'fr' => 'French',
            'es' => 'Spanish',
        ];
        
        // Get available currencies
        $currencies = [
            'KSh' => 'Kenyan Shilling (KSh)',
            'USD' => 'US Dollar ($)',
            'EUR' => 'Euro (€)',
            'GBP' => 'British Pound (£)',
            'TZS' => 'Tanzanian Shilling (TSh)',
            'UGX' => 'Ugandan Shilling (USh)',
        ];

        return view('business.settings.index', compact(
            'business',
            'settings',
            'taxSettings',
            'timezones',
            'languages',
            'currencies'
        ));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:10',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
            'language' => 'required|string|max:10',
        ]);

        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();
        $settings->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'General settings updated successfully!');
    }

    /**
     * Update POS settings
     */
    public function updatePOS(Request $request)
    {
        $validated = $request->validate([
            'auto_print_receipt' => 'boolean',
            'receipt_header' => 'nullable|string|max:255',
            'receipt_footer' => 'nullable|string|max:255',
            'show_logo_on_receipt' => 'boolean',
            'default_payment_method' => 'required|string',
            'require_customer_on_sale' => 'boolean',
        ]);

        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();
        $settings->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'POS settings updated successfully!');
    }

    /**
     * Update inventory settings
     */
    public function updateInventory(Request $request)
    {
        $validated = $request->validate([
            'auto_deduct_stock' => 'boolean',
            'default_low_stock_threshold' => 'required|integer|min:0',
            'allow_negative_stock' => 'boolean',
            'track_stock_movements' => 'boolean',
        ]);

        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();
        $settings->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Inventory settings updated successfully!');
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'enable_email_notifications' => 'boolean',
            'notify_on_new_order' => 'boolean',
            'notify_on_low_stock' => 'boolean',
            'notify_on_new_customer' => 'boolean',
            'daily_sales_report' => 'boolean',
            'weekly_sales_report' => 'boolean',
            'monthly_sales_report' => 'boolean',
            'notification_email' => 'nullable|email',
            'reply_to_email' => 'nullable|email',
            'cc_email' => 'nullable|email',
        ]);

        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();
        $settings->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Notification settings updated successfully!');
    }

    /**
     * Update invoice settings
     */
    public function updateInvoice(Request $request)
    {
        $validated = $request->validate([
            'invoice_prefix' => 'required|string|max:10',
            'receipt_prefix' => 'required|string|max:10',
            'order_prefix' => 'required|string|max:10',
            'invoice_starting_number' => 'required|integer|min:1',
            'invoice_terms' => 'nullable|string',
            'payment_terms_days' => 'required|integer|min:0',
        ]);

        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();
        $settings->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Invoice settings updated successfully!');
    }

    /**
     * Update display settings
     */
    public function updateDisplay(Request $request)
    {
        $validated = $request->validate([
            'items_per_page' => 'required|integer|min:10|max:100',
            'dashboard_layout' => 'required|in:grid,list',
            'show_product_images' => 'boolean',
            'show_stock_levels' => 'boolean',
        ]);

        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();
        $settings->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Display settings updated successfully!');
    }

    /**
     * Update business hours
     */
    public function updateBusinessHours(Request $request)
    {
        $validated = $request->validate([
            'business_hours' => 'required|array',
            'business_hours.*.is_open' => 'required|boolean',
            'business_hours.*.open' => 'nullable|date_format:H:i',
            'business_hours.*.close' => 'nullable|date_format:H:i',
        ]);

        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();
        $settings->update(['business_hours' => $validated['business_hours']]);

        return redirect()->route('settings.index')
            ->with('success', 'Business hours updated successfully!');
    }

    /**
     * Update security settings
     */
    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'require_2fa' => 'boolean',
            'enable_session_timeout' => 'boolean',
            'session_timeout_minutes' => 'required|integer|min:5|max:1440',
        ]);

        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();
        $settings->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Security settings updated successfully!');
    }

    /**
     * Update M-Pesa payment gateway settings
     */
    public function updatePayments(Request $request)
    {
        $validated = $request->validate([
            'mpesa_enabled'     => 'boolean',
            'mpesa_type'        => 'required_if:mpesa_enabled,1|nullable|in:bank,paybill,till',
            'mpesa_bank'        => ['nullable', 'string', 'in:' . implode(',', array_keys(config('mpesa_banks', []))), Rule::requiredIf(fn () => $request->input('mpesa_type') === 'bank')],
            'mpesa_shortcode'   => ['nullable', 'string', 'max:20', Rule::requiredIf(fn () => $request->boolean('mpesa_enabled') && in_array($request->input('mpesa_type'), ['paybill', 'till']))],
            'mpesa_account_ref' => ['nullable', 'string', 'max:50', Rule::requiredIf(fn () => $request->boolean('mpesa_enabled') && $request->input('mpesa_type') === 'bank')],
        ]);

        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();

        // Checkbox sends nothing when unchecked
        $validated['mpesa_enabled'] = $request->boolean('mpesa_enabled');

        // Clear fields irrelevant to the selected mode
        if (($validated['mpesa_type'] ?? null) === 'bank') {
            $validated['mpesa_shortcode'] = null;
        } else {
            $validated['mpesa_bank'] = null;
        }
        if (($validated['mpesa_type'] ?? null) === 'till') {
            $validated['mpesa_account_ref'] = null;
        }

        $settings->update($validated);

        return redirect()->route('settings.index', ['#payments'])
            ->with('success', 'M-Pesa settings saved successfully!');
    }

    /**
     * Reset settings to default
     */
    public function resetToDefaults(Request $request)
    {
        $section = $request->input('section');
        
        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();
        
        $defaults = BusinessSettings::defaults();
        
        // Reset specific section or all
        if ($section && $section !== 'all') {
            // Define which fields belong to which section
            $sectionFields = $this->getSectionFields($section);
            $updateData = array_intersect_key($defaults, array_flip($sectionFields));
        } else {
            $updateData = $defaults;
        }
        
        $settings->update($updateData);

        return redirect()->route('settings.index')
            ->with('success', 'Settings reset to defaults successfully!');
    }

    /**
     * Send an email OTP to the business owner to authorize a data reset.
     * Only the owning account may do this — a team member's active business
     * resolves to the owner's business, so compare user_id explicitly.
     */
    public function sendDataResetCode(Request $request, TwoFactorAuthService $twoFactorService)
    {
        $business = auth()->user()->business;

        if (!$business || $business->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only the business owner can reset business data.',
            ], 403);
        }

        $sent = $twoFactorService->sendVerificationCode(auth()->user(), 'business_reset', [
            'business_id'   => $business->id,
            'business_name' => $business->name,
            'action'        => 'reset',
        ]);

        if ($sent) {
            Log::info('Business data reset code sent', [
                'business_id' => $business->id,
                'user_id'     => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email address.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send verification code. Please try again.',
        ], 500);
    }

    /**
     * Verify the owner's OTP and wipe all transactional data:
     * every sale, stock movement and financial record is deleted and all
     * product / inventory quantities are zeroed. Catalog data (products,
     * services, customers, suppliers, staff, settings) is kept.
     */
    public function verifyAndResetData(Request $request, TwoFactorAuthService $twoFactorService)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $business = auth()->user()->business;

        if (!$business || $business->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only the business owner can reset business data.',
            ], 403);
        }

        $result = $twoFactorService->verifyCode(auth()->user(), 'business_reset', $request->code);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        try {
            $summary = DB::transaction(function () use ($business) {
                $bid = $business->id;
                $summary = [];

                $promotionIds = DB::table('promotions')->where('business_id', $bid)->pluck('id');
                $summary['promotion_usages'] = DB::table('promotion_usages')->whereIn('promotion_id', $promotionIds)->delete();

                // Rental STK requests are kept — the rentals module is not reset.
                $summary['mpesa_stk_requests'] = DB::table('mpesa_stk_requests')
                    ->where('business_id', $bid)->whereNull('rental_payment_id')->delete();
                $summary['customer_debts']     = DB::table('customer_debts')->where('business_id', $bid)->delete();
                $summary['supplier_debts']     = DB::table('supplier_debts')->where('business_id', $bid)->delete();
                $summary['costs']              = DB::table('costs')->where('business_id', $bid)->delete();

                // Service-business sales: bookings, legacy records, their line items.
                $bookingIds = DB::table('service_bookings')->where('business_id', $bid)->pluck('id');
                $serviceIds = DB::table('services')->where('business_id', $bid)->pluck('id');
                $recordIds  = DB::table('service_records')->whereIn('service_id', $serviceIds)->pluck('id');
                $summary['service_items'] = DB::table('service_items')
                    ->whereIn('service_booking_id', $bookingIds)
                    ->orWhereIn('service_record_id', $recordIds)
                    ->orWhereIn('service_id', $serviceIds)
                    ->delete();
                $summary['service_records']  = DB::table('service_records')->whereIn('id', $recordIds)->delete();
                $summary['service_bookings'] = DB::table('service_bookings')->whereIn('id', $bookingIds)->delete();

                $summary['inventory_transactions'] = DB::table('inventory_transactions')->where('business_id', $bid)->delete();
                $summary['stock_receipts']         = DB::table('stock_receipts')->where('business_id', $bid)->delete();

                // Deleting orders cascades to order_items, payments, receipts,
                // invoices, credit notes, returns and their promotion usages.
                $summary['orders'] = DB::table('orders')->where('business_id', $bid)->delete();

                $summary['products_zeroed']        = DB::table('products')->where('business_id', $bid)->update(['stock_quantity' => 0]);
                $summary['inventory_items_zeroed'] = DB::table('inventory_items')->where('business_id', $bid)->update(['current_quantity' => 0]);

                return $summary;
            });

            Log::info('Business data reset after owner verification', [
                'business_id' => $business->id,
                'user_id'     => auth()->id(),
                'summary'     => $summary,
                'ip'          => $request->ip(),
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Business data has been reset — sales records removed and stock set to zero.',
                'redirect' => route('settings.index'),
            ]);
        } catch (\Exception $e) {
            Log::error('Business data reset failed', [
                'business_id' => $business->id,
                'user_id'     => auth()->id(),
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Reset failed — nothing was deleted. Please try again.',
            ], 500);
        }
    }

    /**
     * Get fields for a specific section
     */
    private function getSectionFields($section)
    {
        $sections = [
            'general' => ['currency', 'currency_symbol', 'timezone', 'date_format', 'time_format', 'language'],
            'pos' => ['auto_print_receipt', 'receipt_header', 'receipt_footer', 'show_logo_on_receipt', 'default_payment_method', 'require_customer_on_sale'],
            'inventory' => ['auto_deduct_stock', 'default_low_stock_threshold', 'allow_negative_stock', 'track_stock_movements'],
            'notifications' => ['enable_email_notifications', 'notify_on_new_order', 'notify_on_low_stock', 'notify_on_new_customer', 'daily_sales_report', 'weekly_sales_report', 'monthly_sales_report'],
            'invoice' => ['invoice_prefix', 'receipt_prefix', 'order_prefix', 'invoice_starting_number', 'invoice_terms', 'payment_terms_days'],
            'display' => ['items_per_page', 'dashboard_layout', 'show_product_images', 'show_stock_levels'],
        ];

        return $sections[$section] ?? [];
    }
}
