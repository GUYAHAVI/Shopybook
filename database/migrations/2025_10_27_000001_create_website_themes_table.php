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
        Schema::create('website_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('preview_url')->nullable();
            
            // Design settings
            $table->json('default_colors')->nullable(); // Primary, secondary, accent colors
            $table->json('default_fonts')->nullable(); // Font families
            $table->json('available_sections')->nullable(); // Which section types this theme supports
            $table->json('default_layout')->nullable(); // Layout configuration
            
            // Categorization
            $table->string('category')->default('general'); // business, restaurant, shop, portfolio, etc.
            $table->enum('style', ['modern', 'classic', 'minimal', 'creative', 'professional'])->default('modern');
            
            // Availability
            $table->boolean('is_free')->default(true);
            $table->decimal('price', 8, 2)->nullable(); // For premium themes
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->decimal('rating', 3, 2)->nullable();
            
            $table->timestamps();
            
            $table->index(['is_active', 'is_free']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_themes');
    }
};


