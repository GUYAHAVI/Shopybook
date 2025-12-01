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
        Schema::create('product_conversions', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->unsignedBigInteger('product_id');
            $table->string('conversion_type'); // 'weight_to_area', 'area_to_weight', 'custom'
            $table->string('purchase_unit'); // 'kg', 'sqm', 'pieces', etc.
            $table->string('sale_unit'); // 'kg', 'sqm', 'pieces', etc.
            $table->decimal('conversion_factor', 10, 4); // e.g., 0.2 for 0.2 microns
            $table->decimal('purchase_quantity', 10, 2); // quantity in purchase unit
            $table->decimal('converted_quantity', 10, 2); // calculated quantity in sale unit
            $table->decimal('purchase_cost', 10, 2); // cost per purchase unit
            $table->decimal('sale_price', 10, 2); // price per sale unit
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['business_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_conversions');
    }
};
