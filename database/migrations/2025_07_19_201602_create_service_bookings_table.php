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
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->timestamp('service_date')->useCurrent();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('paid');
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->index(['business_id', 'service_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};
