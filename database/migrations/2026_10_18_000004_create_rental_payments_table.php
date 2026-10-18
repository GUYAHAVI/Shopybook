<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_payments', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->foreignId('rental_tenant_id')->constrained('rental_tenants')->cascadeOnDelete();
            $table->foreignId('rental_unit_id')->constrained('rental_units')->cascadeOnDelete();
            $table->char('period', 7);                     // 'YYYY-MM' the rent covers
            $table->decimal('amount', 12, 2);
            $table->string('method')->default('cash');     // cash|mpesa|bank_transfer|card|cheque
            $table->enum('status', ['paid', 'pending'])->default('paid');
            $table->string('transaction_reference')->nullable(); // M-Pesa receipt, cheque no…
            $table->unsignedBigInteger('mpesa_stk_request_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_payments');
    }
};
