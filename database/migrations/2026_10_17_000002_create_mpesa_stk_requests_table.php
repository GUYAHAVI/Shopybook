<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpesa_stk_requests', function (Blueprint $table) {
            $table->id();
            $table->string('business_id'); // businesses.id is varchar, not bigint
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('phone', 20);
            $table->decimal('amount', 10, 2);
            $table->string('transaction_type', 30); // CustomerPayBillOnline | CustomerBuyGoodsOnline
            $table->string('party_b', 20); // business till/paybill that receives the funds
            $table->string('account_reference', 50)->nullable();
            $table->string('merchant_request_id')->nullable();
            $table->string('checkout_request_id')->unique();
            $table->string('status', 20)->default('pending'); // pending | success | failed
            $table->unsignedInteger('result_code')->nullable();
            $table->string('result_desc')->nullable();
            $table->string('mpesa_receipt_number', 30)->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_stk_requests');
    }
};
