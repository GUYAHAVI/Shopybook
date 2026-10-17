<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            // M-Pesa Daraja STK push — business only supplies its till/paybill number;
            // the platform's org shortcode + passkey sign the request (aggregator model)
            $table->boolean('mpesa_enabled')->default(false)->after('paystack_test_mode');
            $table->string('mpesa_type', 10)->nullable()->after('mpesa_enabled'); // paybill | till
            $table->string('mpesa_shortcode', 20)->nullable()->after('mpesa_type');
            $table->string('mpesa_account_ref', 50)->nullable()->after('mpesa_shortcode');
        });
    }

    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropColumn(['mpesa_enabled', 'mpesa_type', 'mpesa_shortcode', 'mpesa_account_ref']);
        });
    }
};
