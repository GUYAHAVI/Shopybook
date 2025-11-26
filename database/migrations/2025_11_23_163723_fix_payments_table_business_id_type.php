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
        // Drop foreign key if exists
        try {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['business_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist
        }
        
        // Change business_id from bigint to varchar
        DB::statement('ALTER TABLE payments MODIFY business_id VARCHAR(255)');
        
        // Re-add foreign key
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
        });
        
        DB::statement('ALTER TABLE payments MODIFY business_id BIGINT UNSIGNED');
    }
};
