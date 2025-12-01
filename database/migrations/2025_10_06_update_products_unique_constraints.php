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
        Schema::table('products', function (Blueprint $table) {
            // Drop the existing unique constraints
            $table->dropUnique(['slug']);
            $table->dropUnique(['sku']);
            $table->dropUnique(['barcode']);
            
            // Add composite unique constraints (unique per business)
            $table->unique(['business_id', 'slug']);
            $table->unique(['business_id', 'sku']);
            $table->unique(['business_id', 'barcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop composite unique constraints
            $table->dropUnique(['business_id', 'slug']);
            $table->dropUnique(['business_id', 'sku']);
            $table->dropUnique(['business_id', 'barcode']);
            
            // Restore original unique constraints
            $table->unique('slug');
            $table->unique('sku');
            $table->unique('barcode');
        });
    }
};

