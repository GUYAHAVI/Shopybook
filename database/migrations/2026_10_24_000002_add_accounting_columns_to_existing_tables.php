<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('valuation_rate', 15, 4)->nullable()->after('cost_price'); // moving-average cost
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('stock_qty', 12, 3)->nullable()->after('quantity'); // stock units actually deducted
            $table->decimal('unit_cost', 15, 4)->nullable()->after('stock_qty');
            $table->decimal('cost_amount', 15, 2)->nullable()->after('unit_cost');
        });

        Schema::table('costs', function (Blueprint $table) {
            $table->unsignedBigInteger('paid_from_account_id')->nullable()->after('amount');
        });

        Schema::table('stock_receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable()->after('supplier');
            $table->string('payment_mode', 10)->default('paid')->after('total_cost'); // paid | credit
            $table->unsignedBigInteger('paid_from_account_id')->nullable()->after('payment_mode');
        });

        Schema::table('supplier_debts', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_receipt_id')->nullable()->after('supplier_id');
            $table->unsignedBigInteger('expense_account_id')->nullable()->after('stock_receipt_id');
        });

        Schema::table('commission_payouts', function (Blueprint $table) {
            $table->unsignedBigInteger('paid_from_account_id')->nullable()->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('commission_payouts', fn (Blueprint $table) => $table->dropColumn('paid_from_account_id'));
        Schema::table('supplier_debts', fn (Blueprint $table) => $table->dropColumn(['stock_receipt_id', 'expense_account_id']));
        Schema::table('stock_receipts', fn (Blueprint $table) => $table->dropColumn(['supplier_id', 'payment_mode', 'paid_from_account_id']));
        Schema::table('costs', fn (Blueprint $table) => $table->dropColumn('paid_from_account_id'));
        Schema::table('order_items', fn (Blueprint $table) => $table->dropColumn(['stock_qty', 'unit_cost', 'cost_amount']));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('valuation_rate'));
    }
};
