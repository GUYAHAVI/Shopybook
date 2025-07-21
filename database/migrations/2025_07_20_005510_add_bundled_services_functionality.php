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
        Schema::table('services', function (Blueprint $table) {
            // Add fields for service bundling
            $table->boolean('is_bundle_trigger')->default(false)->after('description'); // If true, triggers bundled services
            $table->json('bundled_services')->nullable()->after('is_bundle_trigger'); // Array of service IDs to auto-include
            $table->boolean('is_complimentary')->default(false)->after('bundled_services'); // If true, service is free (commission from parent)
            $table->unsignedBigInteger('parent_service_id')->nullable()->after('is_complimentary'); // ID of parent service for commission calculation
            
            $table->foreign('parent_service_id')->references('id')->on('services')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['parent_service_id']);
            $table->dropColumn(['is_bundle_trigger', 'bundled_services', 'is_complimentary', 'parent_service_id']);
        });
    }
};
