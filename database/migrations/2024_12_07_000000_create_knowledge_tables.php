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
        // Create knowledge_data table
        Schema::create('knowledge_data', function (Blueprint $table) {
            $table->id();
            $table->string('data_type'); // news, social, market, industry, etc.
            $table->string('source'); // NewsAPI, Guardian, Reddit, etc.
            $table->string('category')->nullable(); // business, technology, finance, etc.
            $table->json('data'); // The actual knowledge data
            $table->decimal('relevance_score', 3, 2)->default(0.00); // 0.00 to 1.00
            $table->decimal('sentiment_score', 3, 2)->nullable(); // -1.00 to 1.00
            $table->text('keywords')->nullable(); // Extracted keywords
            $table->string('language')->default('en');
            $table->string('country')->nullable();
            $table->timestamps();
            
            $table->index(['data_type', 'created_at']);
            $table->index(['source', 'created_at']);
            $table->index(['relevance_score', 'created_at']);
        });

        // Create trending_analysis table
        Schema::create('trending_analysis', function (Blueprint $table) {
            $table->id();
            $table->string('topic'); // The trending topic
            $table->string('category')->nullable(); // business, technology, etc.
            $table->integer('mention_count')->default(0);
            $table->decimal('sentiment_score', 3, 2)->nullable(); // -1.00 to 1.00
            $table->decimal('growth_rate', 5, 2)->nullable(); // Percentage growth
            $table->json('sources'); // Array of sources mentioning this topic
            $table->json('related_topics')->nullable(); // Related trending topics
            $table->text('summary')->nullable(); // Brief summary of the trend
            $table->string('trend_direction')->default('neutral'); // rising, falling, stable
            $table->timestamp('peak_time')->nullable(); // When the trend peaked
            $table->timestamps();
            
            $table->index(['topic', 'created_at']);
            $table->index(['category', 'created_at']);
            $table->index(['mention_count', 'created_at']);
            $table->index(['trend_direction', 'created_at']);
        });

        // Create knowledge_insights table for business-specific insights
        Schema::create('knowledge_insights', function (Blueprint $table) {
            $table->id();
            $table->String('business_id');
            $table->string('insight_type'); // market_trend, competitor_analysis, opportunity, etc.
            $table->string('title');
            $table->text('description');
            $table->json('supporting_data')->nullable(); // Supporting knowledge data
            $table->decimal('confidence_score', 3, 2)->default(0.00); // 0.00 to 1.00
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->json('recommendations')->nullable(); // Actionable recommendations
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'insight_type']);
            $table->index(['business_id', 'priority']);
            $table->index(['business_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_insights');
        Schema::dropIfExists('trending_analysis');
        Schema::dropIfExists('knowledge_data');
    }
};
