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
        Schema::table('order_items', function (Blueprint $table) {
            // Add conversion fields for dynamic conversions
            $table->string('sell_unit')->nullable()->after('total'); // kg, sqm, etc.
            $table->string('material_type')->nullable()->after('sell_unit'); // greenhouse_0.2, damliner_0.3, etc.
            $table->decimal('original_quantity', 10, 4)->nullable()->after('material_type'); // Original quantity in sell unit
            $table->decimal('converted_quantity', 10, 4)->nullable()->after('original_quantity'); // Converted quantity
            $table->string('converted_unit')->nullable()->after('converted_quantity'); // Unit after conversion
            $table->decimal('price_per_unit', 10, 2)->nullable()->after('converted_unit'); // Price per unit (kg/sqm)
            $table->json('conversion_data')->nullable()->after('price_per_unit'); // Additional conversion metadata
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'sell_unit',
                'material_type', 
                'original_quantity',
                'converted_quantity',
                'converted_unit',
                'price_per_unit',
                'conversion_data'
            ]);
        });
    }
};
