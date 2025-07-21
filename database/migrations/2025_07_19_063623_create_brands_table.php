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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('slug')->unique();
            $table->json('social_links')->nullable(); // For storing Facebook, Instagram, etc.
            $table->timestamps();
            
            $table->index(['business_id', 'is_active']);
            $table->index(['business_id', 'slug']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
