<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->foreignId('supplier_debt_id')->constrained('supplier_debts')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method', 30);
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            $table->date('paid_at');
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
