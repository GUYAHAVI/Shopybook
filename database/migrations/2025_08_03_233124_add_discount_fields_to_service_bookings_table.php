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
        Schema::table('service_bookings', function (Blueprint $table) {
            // Add discount fields
            $table->enum('discount_type', ['none', 'percentage', 'fixed'])->default('none')->after('total_amount');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            $table->decimal('discount_amount', 10, 2)->nullable()->after('discount_value');
            $table->decimal('subtotal', 10, 2)->nullable()->after('discount_amount'); // Amount before discount
            $table->decimal('final_amount', 10, 2)->nullable()->after('subtotal'); // Amount after discount
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            // Remove discount fields
            $table->dropColumn([
                'discount_type',
                'discount_value', 
                'discount_amount',
                'subtotal',
                'final_amount'
            ]);
        });
    }
};
