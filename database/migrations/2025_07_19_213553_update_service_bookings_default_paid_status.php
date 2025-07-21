<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the default value for payment_status column
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('paid')->change();
        });
        
        // Update existing pending records to paid (since they only record paid services)
        DB::table('service_bookings')
            ->where('payment_status', 'pending')
            ->update(['payment_status' => 'paid']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('pending')->change();
        });
    }
};
