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
        Schema::create('marketing_posts', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Creator
            $table->string('title');
            $table->text('content');
            $table->json('media_files')->nullable(); // Images, videos
            $table->json('hashtags')->nullable();
            $table->json('target_platforms'); // Which platforms to post to
            $table->string('post_type')->default('immediate'); // immediate, scheduled
            $table->timestamp('scheduled_at')->nullable();
            $table->string('status')->default('draft'); // draft, pending, published, failed
            $table->text('ai_suggestions')->nullable(); // AI-generated content suggestions
            $table->json('engagement_data')->nullable(); // Likes, shares, comments aggregated
            $table->timestamps();
            
            $table->index(['business_id', 'status']);
            $table->index(['scheduled_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_posts');
    }
};
