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
        Schema::create('business_apps', function (Blueprint $table) {
            $table->id();
            $table->uuid('business_id');
            $table->string('app_slug'); // e.g., 'sales', 'services', 'marketing'
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // For custom ordering
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->unique(['business_id', 'app_slug']);
            $table->index('business_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_apps');
    }
};
