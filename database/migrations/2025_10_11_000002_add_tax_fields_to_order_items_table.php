<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('total');
            }
            if (! Schema::hasColumn('order_items', 'tax_treatment')) {
                $table->string('tax_treatment', 20)->nullable()->after('tax_amount');
            }
            if (! Schema::hasColumn('order_items', 'unit')) {
                // Snapshot of the product measurement unit at time of sale
                $table->string('unit', 20)->nullable()->after('tax_treatment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'tax_treatment', 'unit']);
        });
    }
};
