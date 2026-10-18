<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * plan was enum('premium','enterprise'), which rejected 'business_addon'
     * (extra business slot) payments under MySQL strict mode.
     */
    public function up(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->string('plan', 30)->change();
            $table->string('gateway', 20)->default('paystack')->after('plan');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_payments', function (Blueprint $table) {
            $table->dropColumn('gateway');
            $table->enum('plan', ['premium', 'enterprise'])->change();
        });
    }
};
