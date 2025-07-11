<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LanguageTranslation;

class AdditionalTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            // Language Switcher
            ['key' => 'select_language', 'sw' => 'Chagua Lugha', 'sheng' => 'Chagua Language'],
            
            // Navigation
            ['key' => 'my_business', 'sw' => 'Biashara Yangu', 'sheng' => 'Biz Yangu'],
            ['key' => 'edit_business_profile', 'sw' => 'Hariri Profaili ya Biashara', 'sheng' => 'Edit Biz Profile'],
            ['key' => 'delete_business', 'sw' => 'Futa Biashara', 'sheng' => 'Futa Biz'],
            ['key' => 'business_analytics', 'sw' => 'Uchambuzi wa Biashara', 'sheng' => 'Biz Analytics'],
            ['key' => 'all_products', 'sw' => 'Bidhaa Zote', 'sheng' => 'Vitu Vyote'],
            ['key' => 'add_new_product', 'sw' => 'Ongeza Bidhaa Mpya', 'sheng' => 'Ongeza Kitu Kipya'],
            ['key' => 'bulk_import', 'sw' => 'Ingiza Kwa Wingi', 'sheng' => 'Bulk Import'],
            ['key' => 'inventory_management', 'sw' => 'Usimamizi wa Hifadhi', 'sheng' => 'Inventory Management'],
            ['key' => 'orders', 'sw' => 'Oda', 'sheng' => 'Orders'],
            ['key' => 'invoices', 'sw' => 'Fakturi', 'sheng' => 'Invoices'],
            ['key' => 'sales_report', 'sw' => 'Ripoti ya Mauzo', 'sheng' => 'Sales Report'],
            ['key' => 'marketing', 'sw' => 'Masoko', 'sheng' => 'Marketing'],
            ['key' => 'promotions', 'sw' => 'Matangazo', 'sheng' => 'Promotions'],
            ['key' => 'bulk_sms', 'sw' => 'SMS Kwa Wingi', 'sheng' => 'Bulk SMS'],
            ['key' => 'email_marketing', 'sw' => 'Masoko ya Barua Pepe', 'sheng' => 'Email Marketing'],
            ['key' => 'advertising', 'sw' => 'Matangazo', 'sheng' => 'Advertising'],
            ['key' => 'marketing_report', 'sw' => 'Ripoti ya Masoko', 'sheng' => 'Marketing Report'],
            ['key' => 'suppliers', 'sw' => 'Wasambazaji', 'sheng' => 'Suppliers'],
            ['key' => 'employees', 'sw' => 'Wafanyakazi', 'sheng' => 'Employees'],
            ['key' => 'manage_team', 'sw' => 'Simamia Timu', 'sheng' => 'Manage Team'],
            ['key' => 'payroll', 'sw' => 'Mshahara', 'sheng' => 'Payroll'],
            ['key' => 'attendance', 'sw' => 'Mahudhurio', 'sheng' => 'Attendance'],
            ['key' => 'ai_assistant', 'sw' => 'Msaidizi wa AI', 'sheng' => 'AI Assistant'],
            ['key' => 'services', 'sw' => 'Huduma', 'sheng' => 'Services'],
            ['key' => 'manage_services', 'sw' => 'Simamia Huduma', 'sheng' => 'Manage Services'],
            ['key' => 'staff', 'sw' => 'Wafanyakazi', 'sheng' => 'Staff'],
            ['key' => 'service_records', 'sw' => 'Rekodi za Huduma', 'sheng' => 'Service Records'],
            ['key' => 'costs', 'sw' => 'Gharama', 'sheng' => 'Costs'],
            ['key' => 'commissions', 'sw' => 'Komisheni', 'sheng' => 'Commissions'],
            
            // Top Navbar
            ['key' => 'quick_actions', 'sw' => 'Vitendo vya Haraka', 'sheng' => 'Quick Actions'],
            ['key' => 'search_products_orders', 'sw' => 'Tafuta bidhaa, oda...', 'sheng' => 'Search products, orders...'],
            ['key' => 'notifications', 'sw' => 'Arifa', 'sheng' => 'Notifications'],
            ['key' => 'new_order', 'sw' => 'Oda Mpya', 'sheng' => 'New Order'],
            ['key' => 'inventory_low', 'sw' => 'Hifadhi ndogo', 'sheng' => 'Inventory low'],
            ['key' => 'on', 'sw' => 'kwenye', 'sheng' => 'on'],
            ['key' => 'ai_business_tip', 'sw' => 'Kidokezo cha AI', 'sheng' => 'AI Business Tip'],
            ['key' => 'user_name', 'sw' => 'Jina la Mtumiaji', 'sheng' => 'User Name'],
            ['key' => 'profile', 'sw' => 'Profaili', 'sheng' => 'Profile'],
            ['key' => 'logout', 'sw' => 'Ondoka', 'sheng' => 'Logout'],
            
            // AI Assistant
            ['key' => 'chat_with_shopybook_ai', 'sw' => 'Ongea na Shopybook AI', 'sheng' => 'Chat with Shopybook AI'],
            ['key' => 'shopybook_ai_assistant', 'sw' => 'Msaidizi wa Shopybook AI', 'sheng' => 'Shopybook AI Assistant'],
            ['key' => 'ai_assistant_greeting', 'sw' => 'Hujambo! Mimi ni Msaidizi wako wa Shopybook AI. Ninawezaje kukusaidia na biashara yako leo?', 'sheng' => 'Hello! I\'m your Shopybook AI Assistant. How can I help you with your business today?'],
            ['key' => 'ask_me_anything', 'sw' => 'Niulize chochote...', 'sheng' => 'Ask me anything...'],
            
            // Delete Business Modal
            ['key' => 'confirm_deletion', 'sw' => 'Thibitisha Ufutaji', 'sheng' => 'Confirm Deletion'],
            ['key' => 'delete_business_confirmation', 'sw' => 'Una uhakika unataka kufuta biashara hii na data yake yote kwa kudumu?', 'sheng' => 'Are you sure you want to permanently delete this business and all its data?'],
            ['key' => 'action_cannot_be_undone', 'sw' => 'Kitendo hiki hakiwezi kurekebishwa!', 'sheng' => 'This action cannot be undone!'],
            ['key' => 'enter_password_to_confirm', 'sw' => 'Weka neno la siri lako kuthibitisha', 'sheng' => 'Enter your password to confirm'],
            ['key' => 'cancel', 'sw' => 'Ghairi', 'sheng' => 'Cancel'],
            
            // Dashboard
            ['key' => 'generate_report', 'sw' => 'Tengeneza Ripoti', 'sheng' => 'Generate Report'],
            ['key' => 'today_orders', 'sw' => 'Oda za Leo', 'sheng' => 'Today\'s Orders'],
            ['key' => 'today_sales', 'sw' => 'Mauzo ya Leo', 'sheng' => 'Today\'s Sales'],
            ['key' => 'conversion_rate', 'sw' => 'Kiwango cha Ubadilishaji', 'sheng' => 'Conversion Rate'],
            ['key' => 'pending_orders', 'sw' => 'Oda zinazosubiri', 'sheng' => 'Pending Orders'],
            ['key' => 'business_analysis', 'sw' => 'Uchambuzi wa Biashara', 'sheng' => 'Business Analysis'],
            ['key' => 'last_7_days', 'sw' => 'Siku 7 za Mwisho', 'sheng' => 'Last 7 Days'],
            ['key' => 'last_30_days', 'sw' => 'Siku 30 za Mwisho', 'sheng' => 'Last 30 Days'],
            ['key' => 'last_quarter', 'sw' => 'Robo ya Mwisho', 'sheng' => 'Last Quarter'],
            ['key' => 'last_year', 'sw' => 'Mwaka wa Mwisho', 'sheng' => 'Last Year'],
            ['key' => 'custom_range', 'sw' => 'Mbalimbali Maalum', 'sheng' => 'Custom Range'],
            ['key' => 'net_profit', 'sw' => 'Faida Halisi', 'sheng' => 'Net Profit'],
            ['key' => 'new_customers', 'sw' => 'Wateja Wapya', 'sheng' => 'New Customers'],
            ['key' => 'returning_rate', 'sw' => 'Kiwango cha Kurudi', 'sheng' => 'Returning Rate'],
        ];

        foreach ($translations as $translation) {
            // English (default)
            LanguageTranslation::updateOrCreate(
                [
                    'language_code' => 'en',
                    'translation_key' => $translation['key'],
                    'context' => 'ui'
                ],
                [
                    'translation_value' => ucfirst(str_replace('_', ' ', $translation['key'])),
                    'is_active' => true
                ]
            );

            // Swahili
            if (isset($translation['sw'])) {
                LanguageTranslation::updateOrCreate(
                    [
                        'language_code' => 'sw',
                        'translation_key' => $translation['key'],
                        'context' => 'ui'
                    ],
                    [
                        'translation_value' => $translation['sw'],
                        'is_active' => true
                    ]
                );
            }

            // Sheng
            if (isset($translation['sheng'])) {
                LanguageTranslation::updateOrCreate(
                    [
                        'language_code' => 'sheng',
                        'translation_key' => $translation['key'],
                        'context' => 'ui'
                    ],
                    [
                        'translation_value' => $translation['sheng'],
                        'is_active' => true
                    ]
                );
            }
        }
    }
} 