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
        Schema::create('salary_advances', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->unsignedBigInteger('staff_id');
            $table->decimal('amount', 10, 2);
            $table->date('advance_date');
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->enum('deduction_status', ['pending', 'deducted', 'partial'])->default('pending');
            $table->decimal('remaining_balance', 10, 2)->default(0);
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['business_id', 'staff_id']);
            $table->index(['advance_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_advances');
    }
};
