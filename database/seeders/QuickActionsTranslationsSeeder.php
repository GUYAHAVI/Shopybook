<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LanguageTranslation;

class QuickActionsTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            // Quick Action Descriptions
            ['key' => 'add_new_product_description', 'sw' => 'Ongeza bidhaa mpya kwenye orodha yako', 'sheng' => 'Add new item to your list'],
            ['key' => 'new_sale_description', 'sw' => 'Anza mauzo mapya na POS', 'sheng' => 'Start new sale with POS'],
            ['key' => 'bulk_import_description', 'sw' => 'Ingiza bidhaa nyingi kwa mara moja', 'sheng' => 'Import many items at once'],
            ['key' => 'business_analytics_description', 'sw' => 'Tazama uchambuzi wa biashara yako', 'sheng' => 'View your business analytics'],
            
            // Additional UI Elements
            ['key' => 'view_all_products', 'sw' => 'Tazama Bidhaa Zote', 'sheng' => 'View All Products'],
            ['key' => 'manage_inventory', 'sw' => 'Simamia Hifadhi', 'sheng' => 'Manage Inventory'],
            ['key' => 'view_orders', 'sw' => 'Tazama Oda', 'sheng' => 'View Orders'],
            ['key' => 'view_customers', 'sw' => 'Tazama Wateja', 'sheng' => 'View Customers'],
            ['key' => 'view_reports', 'sw' => 'Tazama Ripoti', 'sheng' => 'View Reports'],
            ['key' => 'manage_promotions', 'sw' => 'Simamia Matangazo', 'sheng' => 'Manage Promotions'],
            ['key' => 'send_sms', 'sw' => 'Tuma SMS', 'sheng' => 'Send SMS'],
            ['key' => 'send_email', 'sw' => 'Tuma Barua Pepe', 'sheng' => 'Send Email'],
            ['key' => 'manage_staff', 'sw' => 'Simamia Wafanyakazi', 'sheng' => 'Manage Staff'],
            ['key' => 'view_payroll', 'sw' => 'Tazama Mshahara', 'sheng' => 'View Payroll'],
            ['key' => 'manage_suppliers', 'sw' => 'Simamia Wasambazaji', 'sheng' => 'Manage Suppliers'],
            
            // Common Actions
            ['key' => 'view', 'sw' => 'Tazama', 'sheng' => 'View'],
            ['key' => 'create', 'sw' => 'Unda', 'sheng' => 'Create'],
            ['key' => 'update', 'sw' => 'Sasisha', 'sheng' => 'Update'],
            ['key' => 'remove', 'sw' => 'Ondoa', 'sheng' => 'Remove'],
            ['key' => 'export', 'sw' => 'Hamisha', 'sheng' => 'Export'],
            ['key' => 'import', 'sw' => 'Ingiza', 'sheng' => 'Import'],
            ['key' => 'download', 'sw' => 'Pakua', 'sheng' => 'Download'],
            ['key' => 'upload', 'sw' => 'Pakia', 'sheng' => 'Upload'],
            ['key' => 'print', 'sw' => 'Chapa', 'sheng' => 'Print'],
            ['key' => 'share', 'sw' => 'Shiriki', 'sheng' => 'Share'],
            ['key' => 'filter', 'sw' => 'Chuja', 'sheng' => 'Filter'],
            ['key' => 'sort', 'sw' => 'Panga', 'sheng' => 'Sort'],
            ['key' => 'refresh', 'sw' => 'Onyesha Upya', 'sheng' => 'Refresh'],
            ['key' => 'close', 'sw' => 'Funga', 'sheng' => 'Close'],
            ['key' => 'open', 'sw' => 'Fungua', 'sheng' => 'Open'],
            ['key' => 'back', 'sw' => 'Rudi', 'sheng' => 'Back'],
            ['key' => 'next', 'sw' => 'Ifuatayo', 'sheng' => 'Next'],
            ['key' => 'previous', 'sw' => 'Iliyotangulia', 'sheng' => 'Previous'],
            ['key' => 'first', 'sw' => 'Ya Kwanza', 'sheng' => 'First'],
            ['key' => 'last', 'sw' => 'Ya Mwisho', 'sheng' => 'Last'],
            
            // Status Messages
            ['key' => 'success', 'sw' => 'Imefanikiwa', 'sheng' => 'Success'],
            ['key' => 'error', 'sw' => 'Hitilafu', 'sheng' => 'Error'],
            ['key' => 'warning', 'sw' => 'Onyo', 'sheng' => 'Warning'],
            ['key' => 'info', 'sw' => 'Maelezo', 'sheng' => 'Info'],
            ['key' => 'loading', 'sw' => 'Inapakia...', 'sheng' => 'Loading...'],
            ['key' => 'processing', 'sw' => 'Inachakata...', 'sheng' => 'Processing...'],
            ['key' => 'saving', 'sw' => 'Inahifadhi...', 'sheng' => 'Saving...'],
            ['key' => 'deleting', 'sw' => 'Inafuta...', 'sheng' => 'Deleting...'],
            ['key' => 'updating', 'sw' => 'Inasasisha...', 'sheng' => 'Updating...'],
            ['key' => 'creating', 'sw' => 'Inaunda...', 'sheng' => 'Creating...'],
            
            // Time and Date
            ['key' => 'today', 'sw' => 'Leo', 'sheng' => 'Today'],
            ['key' => 'yesterday', 'sw' => 'Jana', 'sheng' => 'Yesterday'],
            ['key' => 'tomorrow', 'sw' => 'Kesho', 'sheng' => 'Tomorrow'],
            ['key' => 'this_week', 'sw' => 'Wiki Hii', 'sheng' => 'This Week'],
            ['key' => 'last_week', 'sw' => 'Wiki Iliyopita', 'sheng' => 'Last Week'],
            ['key' => 'this_month', 'sw' => 'Mwezi Huu', 'sheng' => 'This Month'],
            ['key' => 'last_month', 'sw' => 'Mwezi Uliopita', 'sheng' => 'Last Month'],
            ['key' => 'this_year', 'sw' => 'Mwaka Huu', 'sheng' => 'This Year'],
            ['key' => 'last_year', 'sw' => 'Mwaka Uliopita', 'sheng' => 'Last Year'],
            
            // Currency and Numbers
            ['key' => 'currency', 'sw' => 'Sarafu', 'sheng' => 'Currency'],
            ['key' => 'amount', 'sw' => 'Kiasi', 'sheng' => 'Amount'],
            ['key' => 'quantity', 'sw' => 'Idadi', 'sheng' => 'Quantity'],
            ['key' => 'total', 'sw' => 'Jumla', 'sheng' => 'Total'],
            ['key' => 'subtotal', 'sw' => 'Jumla Ndogo', 'sheng' => 'Subtotal'],
            ['key' => 'tax', 'sw' => 'Kodi', 'sheng' => 'Tax'],
            ['key' => 'discount', 'sw' => 'Punguzo', 'sheng' => 'Discount'],
            ['key' => 'price', 'sw' => 'Bei', 'sheng' => 'Price'],
            ['key' => 'cost', 'sw' => 'Gharama', 'sheng' => 'Cost'],
            ['key' => 'profit', 'sw' => 'Faida', 'sheng' => 'Profit'],
            ['key' => 'loss', 'sw' => 'Hasara', 'sheng' => 'Loss'],
            ['key' => 'revenue', 'sw' => 'Mapato', 'sheng' => 'Revenue'],
            ['key' => 'expenses', 'sw' => 'Gharama', 'sheng' => 'Expenses'],
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