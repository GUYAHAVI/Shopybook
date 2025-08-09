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
        // Add missing columns to knowledge_data table
        Schema::table('knowledge_data', function (Blueprint $table) {
            if (!Schema::hasColumn('knowledge_data', 'source')) {
                $table->string('source')->after('data_type');
            }
            if (!Schema::hasColumn('knowledge_data', 'category')) {
                $table->string('category')->nullable()->after('source');
            }
            if (!Schema::hasColumn('knowledge_data', 'relevance_score')) {
                $table->decimal('relevance_score', 3, 2)->default(0.00)->after('data');
            }
            if (!Schema::hasColumn('knowledge_data', 'sentiment_score')) {
                $table->decimal('sentiment_score', 3, 2)->nullable()->after('relevance_score');
            }
            if (!Schema::hasColumn('knowledge_data', 'keywords')) {
                $table->text('keywords')->nullable()->after('sentiment_score');
            }
            if (!Schema::hasColumn('knowledge_data', 'language')) {
                $table->string('language')->default('en')->after('keywords');
            }
            if (!Schema::hasColumn('knowledge_data', 'country')) {
                $table->string('country')->nullable()->after('language');
            }
        });

        // Add missing columns to trending_analysis table
        Schema::table('trending_analysis', function (Blueprint $table) {
            if (!Schema::hasColumn('trending_analysis', 'topic')) {
                $table->string('topic')->after('id');
            }
            if (!Schema::hasColumn('trending_analysis', 'category')) {
                $table->string('category')->nullable()->after('topic');
            }
            if (!Schema::hasColumn('trending_analysis', 'mention_count')) {
                $table->integer('mention_count')->default(0)->after('category');
            }
            if (!Schema::hasColumn('trending_analysis', 'sentiment_score')) {
                $table->decimal('sentiment_score', 3, 2)->nullable()->after('mention_count');
            }
            if (!Schema::hasColumn('trending_analysis', 'growth_rate')) {
                $table->decimal('growth_rate', 5, 2)->nullable()->after('sentiment_score');
            }
            if (!Schema::hasColumn('trending_analysis', 'sources')) {
                $table->json('sources')->after('growth_rate');
            }
            if (!Schema::hasColumn('trending_analysis', 'related_topics')) {
                $table->json('related_topics')->nullable()->after('sources');
            }
            if (!Schema::hasColumn('trending_analysis', 'summary')) {
                $table->text('summary')->nullable()->after('related_topics');
            }
            if (!Schema::hasColumn('trending_analysis', 'trend_direction')) {
                $table->string('trend_direction')->default('neutral')->after('summary');
            }
            if (!Schema::hasColumn('trending_analysis', 'peak_time')) {
                $table->timestamp('peak_time')->nullable()->after('trend_direction');
            }
        });

        // Add indexes for better performance
        Schema::table('knowledge_data', function (Blueprint $table) {
            if (!Schema::hasIndex('knowledge_data', 'knowledge_data_data_type_created_at_index')) {
                $table->index(['data_type', 'created_at']);
            }
            if (!Schema::hasIndex('knowledge_data', 'knowledge_data_source_created_at_index')) {
                $table->index(['source', 'created_at']);
            }
            if (!Schema::hasIndex('knowledge_data', 'knowledge_data_relevance_score_created_at_index')) {
                $table->index(['relevance_score', 'created_at']);
            }
        });

        Schema::table('trending_analysis', function (Blueprint $table) {
            if (!Schema::hasIndex('trending_analysis', 'trending_analysis_topic_created_at_index')) {
                $table->index(['topic', 'created_at']);
            }
            if (!Schema::hasIndex('trending_analysis', 'trending_analysis_category_created_at_index')) {
                $table->index(['category', 'created_at']);
            }
            if (!Schema::hasIndex('trending_analysis', 'trending_analysis_mention_count_created_at_index')) {
                $table->index(['mention_count', 'created_at']);
            }
            if (!Schema::hasIndex('trending_analysis', 'trending_analysis_trend_direction_created_at_index')) {
                $table->index(['trend_direction', 'created_at']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove columns from knowledge_data table
        Schema::table('knowledge_data', function (Blueprint $table) {
            $table->dropColumn(['source', 'category', 'relevance_score', 'sentiment_score', 'keywords', 'language', 'country']);
        });

        // Remove columns from trending_analysis table
        Schema::table('trending_analysis', function (Blueprint $table) {
            $table->dropColumn(['topic', 'category', 'mention_count', 'sentiment_score', 'growth_rate', 'sources', 'related_topics', 'summary', 'trend_direction', 'peak_time']);
        });
    }
};
