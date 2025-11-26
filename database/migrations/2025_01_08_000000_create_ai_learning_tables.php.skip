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
        // AI Learning Cache Table
        Schema::create('ai_learning_cache', function (Blueprint $table) {
            $table->id();
            $table->uuid('business_id');
            $table->json('learned_data');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->unique(['business_id']);
        });

        // AI Business Advice Table
        Schema::create('ai_business_advice', function (Blueprint $table) {
            $table->id();
            $table->uuid('business_id');
            $table->string('advice_type');
            $table->enum('priority', ['low', 'medium', 'high', 'critical']);
            $table->string('title');
            $table->text('description');
            $table->json('action_items');
            $table->string('expected_impact');
            $table->json('advice_data');
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'priority', 'created_at']);
        });

        // AI Learning Settings Table
        Schema::create('ai_learning_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('business_id');
            $table->boolean('automated_learning_enabled')->default(true);
            $table->boolean('competitor_analysis_enabled')->default(true);
            $table->boolean('market_trends_enabled')->default(true);
            $table->boolean('social_media_learning_enabled')->default(true);
            $table->json('learning_keywords')->nullable();
            $table->json('excluded_competitors')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->unique(['business_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_learning_settings');
        Schema::dropIfExists('ai_business_advice');
        Schema::dropIfExists('ai_learning_cache');
    }
};
