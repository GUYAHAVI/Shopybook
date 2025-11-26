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
        Schema::table('orders', function (Blueprint $table) {
            // Note: 'subtotal' and 'tax' columns already exist from previous migration
            // We're adding additional tax metadata columns
            $table->decimal('tax_rate', 5, 2)->default(0)->after('tax');
            $table->boolean('tax_inclusive')->default(false)->after('tax_rate');
            $table->string('tax_type')->nullable()->after('tax_inclusive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'tax_inclusive', 'tax_type']);
        });
    }
};
