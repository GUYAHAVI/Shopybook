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
        Schema::table('service_bookings', function (Blueprint $table) {
            // Change service_date to use current timestamp as default
            $table->timestamp('service_date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            // Revert back to datetime without default
            $table->datetime('service_date')->change();
        });
    }
};
