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
        Schema::create('post_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('social_media_account_id')->constrained()->onDelete('cascade');
            $table->string('platform_post_id')->nullable(); // ID from the social platform
            $table->string('status'); // pending, published, failed, deleted
            $table->text('platform_response')->nullable(); // Response from API
            $table->text('error_message')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->json('engagement_metrics')->nullable(); // Platform-specific metrics
            $table->timestamps();
            
            $table->index(['marketing_post_id', 'status']);
            $table->index(['social_media_account_id', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_publications');
    }
};
