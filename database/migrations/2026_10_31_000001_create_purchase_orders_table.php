<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->string('po_number');
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('status', 30)->default('draft'); // draft, sent, partially_received, received, cancelled
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->unique(['business_id', 'po_number']);
            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
