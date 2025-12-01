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
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->string('subdomain')->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(false);
            
            // Theme
            $table->foreignId('theme_id')->nullable()->constrained('website_themes')->onDelete('set null');
            
            // Design customization
            $table->json('colors')->nullable(); // Override theme colors
            $table->json('fonts')->nullable(); // Override theme fonts
            $table->json('settings')->nullable(); // General settings (logo, header, footer)
            
            // SEO
            $table->json('seo_settings')->nullable(); // Meta tags, analytics, etc.
            $table->string('favicon_path')->nullable();
            $table->string('logo_path')->nullable();
            
            // Custom domain (future feature)
            $table->string('custom_domain')->nullable();
            $table->boolean('ssl_enabled')->default(false);
            
            // Analytics
            $table->integer('total_views')->default(0);
            $table->integer('total_visits')->default(0);
            $table->timestamp('last_published_at')->nullable();
            
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'is_active']);
            $table->index('subdomain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};


