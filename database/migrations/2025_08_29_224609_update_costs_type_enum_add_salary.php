<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, change the column to string to avoid enum limitations
        Schema::table('costs', function (Blueprint $table) {
            $table->string('type')->change();
        });
        
        // Then add a check constraint to ensure only valid values are used
        DB::statement("ALTER TABLE costs ADD CONSTRAINT chk_cost_type CHECK (type IN ('utility', 'product', 'rent', 'water', 'misc', 'activity', 'renovation', 'salary', 'other'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the check constraint
        DB::statement("ALTER TABLE costs DROP CONSTRAINT chk_cost_type");
        
        // Change back to enum without salary
        Schema::table('costs', function (Blueprint $table) {
            $table->enum('type', ['utility', 'product', 'rent', 'water', 'misc', 'activity', 'renovation', 'other'])->change();
        });
    }
};
