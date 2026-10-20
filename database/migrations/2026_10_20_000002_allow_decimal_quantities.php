<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Allow fractional selling (e.g. 0.5 kg of nails) — POS quantities
        // and stock levels are tracked to 3 decimal places.
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock_quantity', 12, 3)->default(0)->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
        });

        Schema::table('stock_receipts', function (Blueprint $table) {
            $table->decimal('quantity_received', 12, 3)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(0)->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('stock_receipts', function (Blueprint $table) {
            $table->integer('quantity_received')->change();
        });
    }
};
