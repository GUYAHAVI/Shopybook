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
        Schema::create('stock_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->unsignedBigInteger('product_id')->nullable(); // Nullable for new products
            $table->string('receipt_number')->unique();
            $table->string('product_name'); // Store product name for new products
            $table->string('supplier')->nullable();
            $table->integer('quantity_received');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->date('receipt_date');
            $table->string('invoice_number')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('received_by'); // User ID
            $table->enum('receipt_type', ['existing_product', 'new_product'])->default('existing_product');
            $table->json('additional_data')->nullable(); // For storing extra product details for new products
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['business_id', 'receipt_date']);
            $table->index(['business_id', 'product_id']);
            $table->index('receipt_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_receipts');
    }
};



