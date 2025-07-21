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
        Schema::table('service_items', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['service_record_id']);
            
            // Make service_record_id nullable
            $table->unsignedBigInteger('service_record_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_items', function (Blueprint $table) {
            // Make service_record_id non-nullable again
            $table->unsignedBigInteger('service_record_id')->nullable(false)->change();
            
            // Add back the foreign key constraint
            $table->foreign('service_record_id')->references('id')->on('service_records')->onDelete('cascade');
        });
    }
};
