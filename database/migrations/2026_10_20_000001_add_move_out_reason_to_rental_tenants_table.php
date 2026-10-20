<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_tenants', function (Blueprint $table) {
            $table->string('move_out_reason')->nullable()->after('move_out_date');
        });
    }

    public function down(): void
    {
        Schema::table('rental_tenants', function (Blueprint $table) {
            $table->dropColumn('move_out_reason');
        });
    }
};
