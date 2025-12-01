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
        Schema::create('website_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('website_pages')->onDelete('cascade');
            
            // Section type
            $table->string('type'); // hero, about, products, gallery, contact, testimonials, features, team, etc.
            $table->string('component_name')->nullable(); // Vue/React component name
            
            // Content (flexible JSON storage)
            $table->json('content'); // Flexible content storage
            
            // Styling
            $table->json('settings')->nullable(); // Section-specific settings (background, spacing, etc.)
            $table->string('background_type')->default('color'); // color, image, gradient, video
            $table->string('background_value')->nullable();
            $table->json('style_overrides')->nullable(); // Custom CSS/styling
            
            // Visibility
            $table->boolean('is_visible')->default(true);
            $table->boolean('show_on_mobile')->default(true);
            
            // Ordering and animation
            $table->integer('order')->default(0);
            $table->string('animation')->nullable(); // fade-in, slide-up, etc.
            
            $table->timestamps();
            
            $table->index(['page_id', 'order']);
            $table->index(['page_id', 'is_visible']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_sections');
    }
};


