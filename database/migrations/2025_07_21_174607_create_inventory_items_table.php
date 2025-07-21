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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('business_id'); // Changed to string to match businesses table
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category'); // e.g., 'equipment', 'consumable', 'tool', 'product'
            $table->string('unit_type'); // e.g., 'pieces', 'liters', 'kilograms', 'bottles'
            $table->decimal('unit_cost', 10, 2); // cost per unit
            $table->integer('current_quantity')->default(0);
            $table->integer('minimum_quantity')->default(0); // reorder level
            $table->integer('maximum_quantity')->nullable(); // storage capacity
            $table->string('supplier')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['active', 'low_stock', 'out_of_stock', 'discontinued'])->default('active');
            $table->string('storage_location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['business_id', 'category']);
            $table->index(['business_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
