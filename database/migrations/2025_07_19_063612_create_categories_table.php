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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
            
            $table->index(['business_id', 'is_active']);
            $table->index(['business_id', 'parent_id']);
            $table->index(['business_id', 'slug']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
