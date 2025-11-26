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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('receipt_number')->unique();
            $table->json('receipt_data'); // Store all receipt information as JSON
            $table->string('business_name');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method');
            $table->string('currency_symbol')->default('KSh ');
            $table->boolean('is_converted_order')->default(false); // Flag for orders with conversions
            $table->timestamps();
            
            $table->index(['order_id']);
            $table->index(['receipt_number']);
            $table->index(['business_name', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
