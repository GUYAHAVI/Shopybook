<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            // For 'bank' mode: which bank's paybill receives the deposit (config/mpesa_banks.php)
            $table->string('mpesa_bank', 30)->nullable()->after('mpesa_type');
        });
    }

    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropColumn('mpesa_bank');
        });
    }
};
