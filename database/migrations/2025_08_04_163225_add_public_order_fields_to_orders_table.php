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
        Schema::table('orders', function (Blueprint $table) {
            // Public order fields
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->text('delivery_address')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('order_type')->default('internal'); // internal, public_order
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn([
                'customer_name',
                'customer_phone', 
                'customer_email',
                'delivery_address',
                'quantity',
                'unit_price',
                'total_price',
                'order_type',
                'payment_status',
                'product_id'
            ]);
        });
    }
};
