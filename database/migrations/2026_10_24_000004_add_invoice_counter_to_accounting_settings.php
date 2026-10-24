<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounting_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('next_invoice_number')->default(1)->after('next_entry_number');
        });
    }

    public function down(): void
    {
        Schema::table('accounting_settings', fn (Blueprint $table) => $table->dropColumn('next_invoice_number'));
    }
};
