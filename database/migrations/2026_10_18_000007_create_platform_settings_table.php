<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::table('mpesa_stk_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_payment_id')->nullable()->after('rental_payment_id');
            $table->index('subscription_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('mpesa_stk_requests', function (Blueprint $table) {
            $table->dropIndex(['subscription_payment_id']);
            $table->dropColumn('subscription_payment_id');
        });

        Schema::dropIfExists('platform_settings');
    }
};
