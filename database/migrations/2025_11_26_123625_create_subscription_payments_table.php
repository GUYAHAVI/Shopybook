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
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('plan', ['premium', 'enterprise']);
            $table->decimal('amount', 10, 2);
            $table->string('phone_number', 20);
            $table->string('checkout_request_id')->unique();
            $table->string('merchant_request_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('mpesa_receipt_number')->nullable();
            $table->string('transaction_date')->nullable();
            $table->text('result_desc')->nullable();
            $table->timestamps();
            
            $table->index('business_id');
            $table->index('status');
            $table->index('checkout_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
