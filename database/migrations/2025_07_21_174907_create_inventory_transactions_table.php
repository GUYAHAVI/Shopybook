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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('business_id'); // Changed to string to match businesses table
            $table->unsignedBigInteger('inventory_item_id');
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->enum('transaction_type', [
                'purchase',      // Adding new stock
                'usage',         // Used in service
                'wastage',       // Expired/damaged/lost
                'return',        // Returned to supplier
                'transfer',      // Moved between locations
                'adjustment',    // Manual quantity adjustment
                'repair_out',    // Sent for repair
                'repair_in',     // Returned from repair
                'sale'           // Sold as retail item
            ]);
            $table->integer('quantity'); // positive for additions, negative for subtractions
            $table->decimal('unit_cost', 10, 2)->nullable(); // cost per unit at time of transaction
            $table->decimal('total_cost', 10, 2)->nullable(); // total cost of transaction
            $table->string('reference_number')->nullable(); // invoice/receipt number
            $table->text('notes')->nullable();
            $table->date('transaction_date');
            $table->unsignedBigInteger('service_booking_id')->nullable();
            $table->timestamps();
            
            $table->index(['business_id', 'transaction_type']);
            $table->index(['inventory_item_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
