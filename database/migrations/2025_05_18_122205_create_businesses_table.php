<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('businesses', function (Blueprint $table) {
    // Change from $table->id() to string ID for tenancy compatibility
    $table->string('id')->primary();
    
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('email')->nullable();
    $table->string('phone');
    $table->string('business_type');
    $table->text('description')->nullable();
    $table->string('logo_path')->nullable();
    $table->string('cover_path')->nullable();
    $table->json('business_hours')->nullable();
    $table->string('address')->nullable();
    $table->string('city')->nullable();
    $table->string('country')->default('Kenya');
    
    // Required by Laravel Tenancy
    $table->json('data')->nullable();
    
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
