<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            // Paystack payment gateway integration
            $table->boolean('paystack_enabled')->default(false)->after('custom_settings');
            $table->string('paystack_public_key', 100)->nullable()->after('paystack_enabled');
            // Secret key stored encrypted — use text to fit encrypted payload
            $table->text('paystack_secret_key')->nullable()->after('paystack_public_key');
            $table->boolean('paystack_test_mode')->default(true)->after('paystack_secret_key');
        });
    }

    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropColumn(['paystack_enabled', 'paystack_public_key', 'paystack_secret_key', 'paystack_test_mode']);
        });
    }
};
