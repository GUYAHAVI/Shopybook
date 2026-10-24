<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('costs', function (Blueprint $table) {
            $table->timestamp('salary_paid_at')->nullable()->after('paid_from_account_id');
        });
    }

    public function down(): void
    {
        Schema::table('costs', fn (Blueprint $table) => $table->dropColumn('salary_paid_at'));
    }
};
