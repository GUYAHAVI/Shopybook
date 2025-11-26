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
        Schema::create('website_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            
            // Page info
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            
            // Page type
            $table->boolean('is_homepage')->default(false);
            $table->boolean('is_published')->default(false);
            $table->boolean('show_in_menu')->default(true);
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_image')->nullable(); // Open Graph image for social sharing
            
            // Layout
            $table->string('layout')->default('default'); // default, full-width, sidebar, etc.
            $table->json('page_settings')->nullable();
            
            // Ordering
            $table->integer('order')->default(0);
            
            // Analytics
            $table->integer('views')->default(0);
            
            $table->timestamps();
            
            $table->unique(['website_id', 'slug']);
            $table->index(['website_id', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_pages');
    }
};

