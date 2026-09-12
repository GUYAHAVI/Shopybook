<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->integer('ai_credits')->default(0)->after('plan');
            $table->integer('ai_credits_purchased')->default(0)->after('ai_credits');
            $table->timestamp('ai_credits_reset_at')->nullable()->after('ai_credits_purchased');
            $table->json('ai_credit_log')->nullable()->after('ai_credits_reset_at');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['ai_credits', 'ai_credits_purchased', 'ai_credits_reset_at', 'ai_credit_log']);
        });
    }
};
