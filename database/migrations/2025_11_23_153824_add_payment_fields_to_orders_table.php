<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update existing 'pending' values to 'unpaid'
        DB::table('orders')->where('payment_status', 'pending')->update(['payment_status' => 'unpaid']);
        
        Schema::table('orders', function (Blueprint $table) {
            // Only add columns if they don't exist
            if (!Schema::hasColumn('orders', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'balance_due')) {
                $table->decimal('balance_due', 10, 2)->default(0)->after('amount_paid');
            }
            // Always update the enum to include 'partial'
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->change();
        });
        
        // Set balance_due for existing unpaid orders
        DB::statement('UPDATE orders SET balance_due = total_amount WHERE payment_status = "unpaid"');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'balance_due']);
            $table->enum('payment_status', ['pending', 'paid'])->default('pending')->change();
        });
    }
};
