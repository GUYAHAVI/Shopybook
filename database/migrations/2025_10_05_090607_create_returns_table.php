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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('return_number')->unique();
            
            // Return Details
            $table->enum('return_type', ['full', 'partial'])->default('full');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('reason')->nullable();
            $table->enum('reason_category', [
                'defective',
                'wrong_item',
                'not_as_described',
                'customer_changed_mind',
                'damaged_in_shipping',
                'other'
            ])->default('other');
            
            // Financial Details
            $table->decimal('original_amount', 10, 2);
            $table->decimal('refund_amount', 10, 2);
            $table->decimal('restocking_fee', 10, 2)->default(0);
            $table->enum('refund_method', ['cash', 'card', 'mobile_money', 'bank_transfer', 'store_credit'])->nullable();
            $table->boolean('refund_processed')->default(false);
            $table->timestamp('refund_processed_at')->nullable();
            
            // Inventory Details
            $table->boolean('return_to_stock')->default(true);
            $table->boolean('stock_returned')->default(false);
            $table->text('items_data')->nullable(); // JSON data of returned items
            
            // Processing Details
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
