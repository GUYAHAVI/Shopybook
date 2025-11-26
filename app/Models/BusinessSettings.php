<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        // Regional Settings
        'currency',
        'currency_symbol',
        'timezone',
        'date_format',
        'time_format',
        'language',
        // POS Settings
        'auto_print_receipt',
        'receipt_header',
        'receipt_footer',
        'show_logo_on_receipt',
        'default_payment_method',
        'require_customer_on_sale',
        // Inventory Settings
        'auto_deduct_stock',
        'default_low_stock_threshold',
        'allow_negative_stock',
        'track_stock_movements',
        // Notification Settings
        'enable_email_notifications',
        'notify_on_new_order',
        'notify_on_low_stock',
        'notify_on_new_customer',
        'daily_sales_report',
        'weekly_sales_report',
        'monthly_sales_report',
        // Invoice & Receipt Settings
        'invoice_prefix',
        'receipt_prefix',
        'order_prefix',
        'invoice_starting_number',
        'invoice_terms',
        'payment_terms_days',
        // Email Settings
        'notification_email',
        'reply_to_email',
        'cc_email',
        // Security Settings
        'require_2fa',
        'enable_session_timeout',
        'session_timeout_minutes',
        // Business Hours
        'business_hours',
        'holidays',
        // Display Settings
        'items_per_page',
        'dashboard_layout',
        'show_product_images',
        'show_stock_levels',
        // Additional Settings
        'custom_settings',
    ];

    protected $casts = [
        // Boolean casts
        'auto_print_receipt' => 'boolean',
        'show_logo_on_receipt' => 'boolean',
        'require_customer_on_sale' => 'boolean',
        'auto_deduct_stock' => 'boolean',
        'allow_negative_stock' => 'boolean',
        'track_stock_movements' => 'boolean',
        'enable_email_notifications' => 'boolean',
        'notify_on_new_order' => 'boolean',
        'notify_on_low_stock' => 'boolean',
        'notify_on_new_customer' => 'boolean',
        'daily_sales_report' => 'boolean',
        'weekly_sales_report' => 'boolean',
        'monthly_sales_report' => 'boolean',
        'require_2fa' => 'boolean',
        'enable_session_timeout' => 'boolean',
        'show_product_images' => 'boolean',
        'show_stock_levels' => 'boolean',
        // Integer casts
        'default_low_stock_threshold' => 'integer',
        'invoice_starting_number' => 'integer',
        'payment_terms_days' => 'integer',
        'session_timeout_minutes' => 'integer',
        'items_per_page' => 'integer',
        // JSON casts
        'business_hours' => 'array',
        'holidays' => 'array',
        'custom_settings' => 'array',
    ];

    /**
     * Get the business that owns the settings
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get default settings
     */
    public static function defaults()
    {
        return [
            'currency' => 'KSh',
            'currency_symbol' => 'KSh ',
            'timezone' => 'Africa/Nairobi',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'language' => 'en',
            'auto_print_receipt' => false,
            'show_logo_on_receipt' => true,
            'default_payment_method' => 'cash',
            'require_customer_on_sale' => false,
            'auto_deduct_stock' => true,
            'default_low_stock_threshold' => 10,
            'allow_negative_stock' => false,
            'track_stock_movements' => true,
            'enable_email_notifications' => true,
            'notify_on_new_order' => true,
            'notify_on_low_stock' => true,
            'notify_on_new_customer' => false,
            'invoice_prefix' => 'INV',
            'receipt_prefix' => 'RCP',
            'order_prefix' => 'ORD',
            'invoice_starting_number' => 1001,
            'payment_terms_days' => 30,
            'items_per_page' => 20,
            'dashboard_layout' => 'grid',
            'show_product_images' => true,
            'show_stock_levels' => true,
        ];
    }

    /**
     * Get formatted timezone
     */
    public function getFormattedTimezoneAttribute()
    {
        return str_replace('_', ' ', $this->timezone);
    }

    /**
     * Check if business is currently open
     */
    public function isOpen()
    {
        if (!$this->business_hours) {
            return true; // If no hours set, assume always open
        }

        $now = now($this->timezone);
        $dayOfWeek = strtolower($now->format('l'));
        
        if (!isset($this->business_hours[$dayOfWeek])) {
            return false;
        }

        $hours = $this->business_hours[$dayOfWeek];
        if (!$hours['is_open']) {
            return false;
        }

        $currentTime = $now->format('H:i');
        return $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
    }
}
