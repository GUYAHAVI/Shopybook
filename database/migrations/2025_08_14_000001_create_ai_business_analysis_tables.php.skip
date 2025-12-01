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
        Schema::create('ai_business_analysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('analysis_type')->default('comprehensive');
            $table->longText('analysis_data'); // JSON data from the AI model
            $table->decimal('predicted_income', 12, 2)->nullable();
            $table->decimal('current_income', 12, 2)->nullable();
            $table->decimal('improvement_potential', 12, 2)->nullable();
            $table->string('model_version')->default('canadian_msme_v1');
            $table->float('confidence_score')->nullable();
            $table->timestamps();
            
            $table->index(['business_id', 'created_at']);
            $table->index('analysis_type');
        });

        Schema::create('ai_business_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('analysis_id')->constrained('ai_business_analysis')->onDelete('cascade');
            $table->string('category'); // revenue_optimization, cost_management, etc.
            $table->string('priority'); // high, medium, low
            $table->string('title');
            $table->text('description');
            $table->json('action_items')->nullable();
            $table->string('expected_impact')->nullable();
            $table->boolean('is_implemented')->default(false);
            $table->timestamp('implemented_at')->nullable();
            $table->text('implementation_notes')->nullable();
            $table->timestamps();
            
            $table->index(['business_id', 'priority']);
            $table->index('category');
        });

        Schema::create('ai_model_performance', function (Blueprint $table) {
            $table->id();
            $table->string('model_name');
            $table->string('version');
            $table->json('metrics'); // accuracy, precision, recall, etc.
            $table->timestamp('last_trained_at')->nullable();
            $table->integer('predictions_made')->default(0);
            $table->float('average_accuracy')->nullable();
            $table->timestamps();
            
            $table->unique(['model_name', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_business_recommendations');
        Schema::dropIfExists('ai_business_analysis');
        Schema::dropIfExists('ai_model_performance');
    }
};
