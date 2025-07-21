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
        Schema::table('service_items', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->nullable()->after('amount'); // Commission rate percentage
            $table->decimal('commission_amount', 10, 2)->nullable()->after('commission_rate'); // Calculated commission amount
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_items', function (Blueprint $table) {
            $table->dropColumn(['commission_rate', 'commission_amount']);
        });
    }
};
