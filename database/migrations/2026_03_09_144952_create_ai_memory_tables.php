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
        // 1. Conversation history — stores every chat turn for multi-turn context
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('business_id', 36)->index();
            $table->string('session_id', 64)->index();
            $table->enum('role', ['user', 'assistant']);
            $table->text('content');
            $table->unsignedSmallInteger('tokens')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'session_id', 'created_at']);
        });

        // 2. Business memory — extracted facts about this specific business
        //    owner's goals, preferences, pain points; persists across sessions
        Schema::create('ai_business_memory', function (Blueprint $table) {
            $table->id();
            $table->string('business_id', 36)->index();
            $table->string('facet_key', 100);
            $table->text('facet_value');
            $table->tinyInteger('confidence')->default(70); // 0-100
            $table->string('source', 30)->default('chat'); // 'chat' | 'analysis'
            $table->timestamps();

            $table->unique(['business_id', 'facet_key']);
        });

        // 3. Kenyan market intelligence — cached from public RSS feeds so we
        //    don't call external APIs at chat-time; pruned after 60 days
        Schema::create('ai_market_insights', function (Blueprint $table) {
            $table->id();
            $table->string('category', 60)->index(); // maps to business_type
            $table->string('title', 300);
            $table->text('summary');
            $table->string('source_url', 500)->nullable();
            $table->string('source_name', 100)->nullable();
            $table->json('keywords')->nullable();
            $table->tinyInteger('relevance_score')->default(50);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();

            $table->index(['category', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_market_insights');
        Schema::dropIfExists('ai_business_memory');
        Schema::dropIfExists('ai_conversations');
    }
};
