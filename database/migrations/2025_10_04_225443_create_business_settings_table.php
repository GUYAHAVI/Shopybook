<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_id')->unique();
            
            // Regional Settings
            $table->string('currency', 10)->default('KSh');
            $table->string('currency_symbol', 10)->default('KSh ');
            $table->string('timezone')->default('Africa/Nairobi');
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('H:i');
            $table->string('language')->default('en');
            
            // POS Settings
            $table->boolean('auto_print_receipt')->default(false);
            $table->string('receipt_header')->nullable();
            $table->string('receipt_footer')->nullable();
            $table->boolean('show_logo_on_receipt')->default(true);
            $table->string('default_payment_method')->default('cash');
            $table->boolean('require_customer_on_sale')->default(false);
            
            // Inventory Settings
            $table->boolean('auto_deduct_stock')->default(true);
            $table->integer('default_low_stock_threshold')->default(10);
            $table->boolean('allow_negative_stock')->default(false);
            $table->boolean('track_stock_movements')->default(true);
            
            // Notification Settings
            $table->boolean('enable_email_notifications')->default(true);
            $table->boolean('notify_on_new_order')->default(true);
            $table->boolean('notify_on_low_stock')->default(true);
            $table->boolean('notify_on_new_customer')->default(false);
            $table->boolean('daily_sales_report')->default(false);
            $table->boolean('weekly_sales_report')->default(false);
            $table->boolean('monthly_sales_report')->default(false);
            
            // Invoice & Receipt Settings
            $table->string('invoice_prefix')->default('INV');
            $table->string('receipt_prefix')->default('RCP');
            $table->string('order_prefix')->default('ORD');
            $table->integer('invoice_starting_number')->default(1001);
            $table->text('invoice_terms')->nullable();
            $table->integer('payment_terms_days')->default(30);
            
            // Email Settings
            $table->string('notification_email')->nullable();
            $table->string('reply_to_email')->nullable();
            $table->string('cc_email')->nullable();
            
            // Security Settings
            $table->boolean('require_2fa')->default(false);
            $table->boolean('enable_session_timeout')->default(false);
            $table->integer('session_timeout_minutes')->default(60);
            
            // Business Hours
            $table->json('business_hours')->nullable();
            $table->json('holidays')->nullable();
            
            // Display Settings
            $table->integer('items_per_page')->default(20);
            $table->string('dashboard_layout')->default('grid');
            $table->boolean('show_product_images')->default(true);
            $table->boolean('show_stock_levels')->default(true);
            
            // Additional Settings (JSON for flexibility)
            $table->json('custom_settings')->nullable();
            
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
