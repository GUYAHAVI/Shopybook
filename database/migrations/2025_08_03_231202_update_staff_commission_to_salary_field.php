<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            // Add new salary field (integer for fixed amount)
            $table->integer('salary')->nullable()->after('role');
            
            // Drop the old commission_rate column
            $table->dropColumn('commission_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            // Restore commission_rate column
            $table->decimal('commission_rate', 5, 2)->nullable()->after('role');
            
            // Drop the salary column
            $table->dropColumn('salary');
        });
    }
};
