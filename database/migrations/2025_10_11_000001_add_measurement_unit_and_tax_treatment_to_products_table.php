<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'measurement_unit')) {
                $table->string('measurement_unit', 20)->nullable()->after('stock_quantity');
            }
            if (! Schema::hasColumn('products', 'tax_treatment')) {
                // null = inherit business tax settings; inclusive | exclusive | exempt
                $table->string('tax_treatment', 20)->nullable()->after('price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['measurement_unit', 'tax_treatment']);
        });
    }
};
