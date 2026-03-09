<?php

namespace App\Http\Controllers;

use App\Models\BusinessSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
     * Update Paystack payment gateway settings
     */
    public function updatePayments(Request $request)
    {
        $validated = $request->validate([
            'paystack_enabled'    => 'boolean',
            'paystack_public_key' => 'nullable|string|max:100',
            'paystack_secret_key' => 'nullable|string|max:255',
            'paystack_test_mode'  => 'boolean',
        ]);

        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();

        // Never overwrite an existing secret key with an empty string
        if (empty($validated['paystack_secret_key'])) {
            unset($validated['paystack_secret_key']);
        }

        $settings->update($validated);

        return redirect()->route('settings.index', ['#payments'])
            ->with('success', 'Payment settings saved successfully!');
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
