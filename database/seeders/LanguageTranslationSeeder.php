<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LanguageTranslation;

class LanguageTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            // Dashboard
            ['key' => 'dashboard', 'sw' => 'Dashibodi', 'sheng' => 'Dash'],
            ['key' => 'total_sales', 'sw' => 'Jumla ya Mauzo', 'sheng' => 'Mauzo yote'],
            ['key' => 'total_products', 'sw' => 'Jumla ya Bidhaa', 'sheng' => 'Vitu vyote'],
            ['key' => 'total_customers', 'sw' => 'Jumla ya Wateja', 'sheng' => 'Wateja wote'],
            ['key' => 'monthly_sales', 'sw' => 'Mauzo ya Mwezi', 'sheng' => 'Mauzo ya mwezi'],
            ['key' => 'low_stock_items', 'sw' => 'Bidhaa zenye Hifadhi Ndogo', 'sheng' => 'Vitu vya mwisho'],
            ['key' => 'new_customers', 'sw' => 'Wateja Wapya', 'sheng' => 'Wateja wapya'],
            ['key' => 'recent_transactions', 'sw' => 'Shughuli za Hivi Karibuni', 'sheng' => 'Miamala ya juzi'],
            ['key' => 'top_selling_products', 'sw' => 'Bidhaa Zinazouzwa Zaidi', 'sheng' => 'Vitu vinauzwa sana'],
            
            // Navigation
            ['key' => 'products', 'sw' => 'Bidhaa', 'sheng' => 'Vitu'],
            ['key' => 'sales', 'sw' => 'Mauzo', 'sheng' => 'Mauzo'],
            ['key' => 'customers', 'sw' => 'Wateja', 'sheng' => 'Wateja'],
            ['key' => 'inventory', 'sw' => 'Hifadhi', 'sheng' => 'Stocki'],
            ['key' => 'reports', 'sw' => 'Ripoti', 'sheng' => 'Ripoti'],
            ['key' => 'settings', 'sw' => 'Mipangilio', 'sheng' => 'Mpangilio'],
            ['key' => 'categories', 'sw' => 'Kategoria', 'sheng' => 'Makundi'],
            ['key' => 'suppliers', 'sw' => 'Wauzaji', 'sheng' => 'Wasambazaji'],
            ['key' => 'employees', 'sw' => 'Wafanyakazi', 'sheng' => 'Wafanyikazi'],
            
            // Actions
            ['key' => 'add', 'sw' => 'Ongeza', 'sheng' => 'Add'],
            ['key' => 'edit', 'sw' => 'Hariri', 'sheng' => 'Edit'],
            ['key' => 'delete', 'sw' => 'Futa', 'sheng' => 'Futa'],
            ['key' => 'save', 'sw' => 'Hifadhi', 'sheng' => 'Save'],
            ['key' => 'cancel', 'sw' => 'Ghairi', 'sheng' => 'Drop'],
            ['key' => 'search', 'sw' => 'Tafuta', 'sheng' => 'Piga search'],
            ['key' => 'confirm', 'sw' => 'Thibitisha', 'sheng' => 'Confirm'],
            ['key' => 'approve', 'sw' => 'Idhinisha', 'sheng' => 'Approve'],
            ['key' => 'reject', 'sw' => 'Kataa', 'sheng' => 'Reject'],
            ['key' => 'view_details', 'sw' => 'Tazama Maelezo', 'sheng' => 'Angalia details'],
            
            // Product Management
            ['key' => 'product_name', 'sw' => 'Jina la Bidhaa', 'sheng' => 'Jina la item'],
            ['key' => 'product_price', 'sw' => 'Bei ya Bidhaa', 'sheng' => 'Pesa ya item'],
            ['key' => 'product_category', 'sw' => 'Aina ya Bidhaa', 'sheng' => 'Kundi la item'],
            ['key' => 'stock_quantity', 'sw' => 'Idadi ya Hifadhi', 'sheng' => 'Idadi ya stock'],
            ['key' => 'add_product', 'sw' => 'Ongeza Bidhaa', 'sheng' => 'Add item'],
            ['key' => 'edit_product', 'sw' => 'Hariri Bidhaa', 'sheng' => 'Edit item'],
            ['key' => 'product_description', 'sw' => 'Maelezo ya Bidhaa', 'sheng' => 'Maelezo ya item'],
            ['key' => 'barcode', 'sw' => 'Msimbo wa Mstari', 'sheng' => 'Barcode'],
            ['key' => 'sku', 'sw' => 'SKU', 'sheng' => 'SKU'],
            ['key' => 'expiry_date', 'sw' => 'Tarehe ya Kuumia', 'sheng' => 'Tarehe ya kumaliza'],
            
            // Sales
            ['key' => 'pos_system', 'sw' => 'Mfumo wa POS', 'sheng' => 'POS system'],
            ['key' => 'new_sale', 'sw' => 'Mauzo Mapya', 'sheng' => 'New sale'],
            ['key' => 'total_amount', 'sw' => 'Jumla ya Kiasi', 'sheng' => 'Total amount'],
            ['key' => 'payment_method', 'sw' => 'Njia ya Malipo', 'sheng' => 'Njia ya kulipa'],
            ['key' => 'cash', 'sw' => 'Pesa Taslimu', 'sheng' => 'Cash'],
            ['key' => 'mpesa', 'sw' => 'M-Pesa', 'sheng' => 'M-Pesa'],
            ['key' => 'card', 'sw' => 'Kadi', 'sheng' => 'Card'],
            ['key' => 'credit', 'sw' => 'Mikopo', 'sheng' => 'Credit'],
            ['key' => 'invoice', 'sw' => 'Anuarisha', 'sheng' => 'Invoice'],
            ['key' => 'receipt', 'sw' => 'Risiti', 'sheng' => 'Receipt'],
            ['key' => 'discount', 'sw' => 'Punguzo', 'sheng' => 'Discount'],
            ['key' => 'tax', 'sw' => 'Kodi', 'sheng' => 'Tax'],
            ['key' => 'subtotal', 'sw' => 'Jumla Ndogo', 'sheng' => 'Subtotal'],
            
            // Customer Management
            ['key' => 'customer_name', 'sw' => 'Jina la Mteja', 'sheng' => 'Jina la mteja'],
            ['key' => 'customer_phone', 'sw' => 'Simu ya Mteja', 'sheng' => 'Namba ya mteja'],
            ['key' => 'customer_email', 'sw' => 'Barua Pepe ya Mteja', 'sheng' => 'Email ya mteja'],
            ['key' => 'add_customer', 'sw' => 'Ongeza Mteja', 'sheng' => 'Add mteja'],
            ['key' => 'customer_address', 'sw' => 'Anwani ya Mteja', 'sheng' => 'Location ya mteja'],
            ['key' => 'customer_since', 'sw' => 'Mteja Tangu', 'sheng' => 'Mteja since'],
            ['key' => 'loyalty_points', 'sw' => 'Pointi za Uaminifu', 'sheng' => 'Loyalty points'],
            
            // Business Analysis
            ['key' => 'business_analysis', 'sw' => 'Uchambuzi wa Biashara', 'sheng' => 'Biz analysis'],
            ['key' => 'generate_analysis', 'sw' => 'Tengeneza Uchambuzi', 'sheng' => 'Generate analysis'],
            ['key' => 'sales_analysis', 'sw' => 'Uchambuzi wa Mauzo', 'sheng' => 'Sales analysis'],
            ['key' => 'financial_report', 'sw' => 'Ripoti ya Fedha', 'sheng' => 'Financial report'],
            ['key' => 'ai_insights', 'sw' => 'Ufahamu wa AI', 'sheng' => 'AI insights'],
            ['key' => 'sales_trends', 'sw' => 'Mienendo ya Mauzo', 'sheng' => 'Sales trends'],
            ['key' => 'performance_metrics', 'sw' => 'Vipimo ya Utendaji', 'sheng' => 'Performance metrics'],
            
            // Notifications
            ['key' => 'low_stock_alert', 'sw' => 'Onyo la Hifadhi Ndogo', 'sheng' => 'Low stock alert'],
            ['key' => 'new_order', 'sw' => 'Oda Mpya', 'sheng' => 'New order'],
            ['key' => 'payment_received', 'sw' => 'Malipo Yamepokelewa', 'sheng' => 'Payment received'],
            ['key' => 'order_shipped', 'sw' => 'Oda Imetumwa', 'sheng' => 'Order shipped'],
            ['key' => 'order_delivered', 'sw' => 'Oda Imefikishwa', 'sheng' => 'Order delivered'],
            ['key' => 'new_message', 'sw' => 'Ujumbe Mpya', 'sheng' => 'New message'],
            
            // Authentication
            ['key' => 'login', 'sw' => 'Ingia', 'sheng' => 'Login'],
            ['key' => 'logout', 'sw' => 'Ondoka', 'sheng' => 'Logout'],
            ['key' => 'register', 'sw' => 'Jisajili', 'sheng' => 'Register'],
            ['key' => 'forgot_password', 'sw' => 'Umesahau Nywila?', 'sheng' => 'Forgot password?'],
            ['key' => 'reset_password', 'sw' => 'Weka Upya Nywila', 'sheng' => 'Reset password'],
            ['key' => 'remember_me', 'sw' => 'Nikumbuke', 'sheng' => 'Remember me'],
            
            // User Profile
            ['key' => 'profile', 'sw' => 'Wasifu', 'sheng' => 'Profile'],
            ['key' => 'account_settings', 'sw' => 'Mipangilio ya Akaunti', 'sheng' => 'Account settings'],
            ['key' => 'change_password', 'sw' => 'Badilisha Nenosiri', 'sheng' => 'Change password'],
            ['key' => 'my_orders', 'sw' => 'Oda Zangu', 'sheng' => 'My orders'],
            ['key' => 'wishlist', 'sw' => 'Orodha ya Matamanio', 'sheng' => 'Wishlist'],
            
            // Statuses
            ['key' => 'active', 'sw' => 'Inatumika', 'sheng' => 'Active'],
            ['key' => 'inactive', 'sw' => 'Haitumiki', 'sheng' => 'Inactive'],
            ['key' => 'pending', 'sw' => 'Inasubiri', 'sheng' => 'Pending'],
            ['key' => 'completed', 'sw' => 'Imekamilika', 'sheng' => 'Completed'],
            ['key' => 'cancelled', 'sw' => 'Imeshindwa', 'sheng' => 'Cancelled'],
            ['key' => 'refunded', 'sw' => 'Imerudishwa', 'sheng' => 'Refunded'],
            
            // Additional Sheng-specific translations
            ['key' => 'quick_sale', 'sheng' => 'Quick sale'],
            ['key' => 'chill', 'sheng' => 'Poa'],
            ['key' => 'cool', 'sheng' => 'Fit'],
            ['key' => 'awesome', 'sheng' => 'Mbao'],
            ['key' => 'expensive', 'sheng' => 'Expi'],
            ['key' => 'cheap', 'sheng' => 'Bei poa'],
            ['key' => 'friend', 'sheng' => 'Msee'],
            ['key' => 'manager', 'sheng' => 'Boss'],
            ['key' => 'money', 'sheng' => 'Pesa'],
            ['key' => 'business', 'sheng' => 'Biz'],
            ['key' => 'good', 'sheng' => 'Safi'],
            ['key' => 'bad', 'sheng' => 'Mbaya'],
            ['key' => 'problem', 'sheng' => 'Shida'],
            ['key' => 'solution', 'sheng' => 'Suluhisho'],
            ['key' => 'help', 'sheng' => 'Msaada'],
            ['key' => 'quick', 'sheng' => 'Haraka'],
            ['key' => 'easy', 'sheng' => 'Rahisi'],
            ['key' => 'difficult', 'sheng' => 'Ngumu'],
            ['key' => 'profit', 'sheng' => 'Faida'],
            ['key' => 'loss', 'sheng' => 'Hasara'],
            ['key' => 'shop', 'sheng' => 'Duka'],
            ['key' => 'buy', 'sheng' => 'Nunua'],
            ['key' => 'sell', 'sheng' => 'Uza'],
            ['key' => 'pricey', 'sheng' => 'Bei kali'],
            ['key' => 'bargain', 'sheng' => 'Punguza bei'],
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