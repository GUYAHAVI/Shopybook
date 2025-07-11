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
        Schema::create('language_translations', function (Blueprint $table) {
            $table->id();
            $table->string('language_code', 10); // en, sw, sheng
            $table->string('translation_key', 100);
            $table->text('translation_value');
            $table->string('context', 100)->nullable(); // ui, ai_analysis, notifications, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['language_code', 'translation_key', 'context'], 'lang_trans_unique');
            $table->index(['language_code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language_translations');
    }
};
