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
        Schema::table('stock_receipts', function (Blueprint $table) {
            // Increase total_cost precision from DECIMAL(10,2) to DECIMAL(15,2)
            // This allows values up to 9,999,999,999,999.99 (nearly 10 trillion)
            $table->decimal('total_cost', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_receipts', function (Blueprint $table) {
            // Revert back to original precision
            $table->decimal('total_cost', 10, 2)->nullable()->change();
        });
    }
};






