<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_order_id')->nullable()->after('supplier_id');
            $table->unsignedBigInteger('purchase_order_item_id')->nullable()->after('purchase_order_id');
            $table->index('purchase_order_id');
        });

        Schema::table('supplier_debts', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_order_id')->nullable()->after('stock_receipt_id');
            $table->index('purchase_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_receipts', function (Blueprint $table) {
            $table->dropIndex(['purchase_order_id']);
            $table->dropColumn(['purchase_order_id', 'purchase_order_item_id']);
        });

        Schema::table('supplier_debts', function (Blueprint $table) {
            $table->dropIndex(['purchase_order_id']);
            $table->dropColumn('purchase_order_id');
        });
    }
};
