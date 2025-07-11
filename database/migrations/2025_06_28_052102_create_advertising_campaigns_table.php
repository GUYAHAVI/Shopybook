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
        Schema::create('advertising_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('platform', ['facebook', 'instagram', 'google', 'email', 'sms']);
            $table->decimal('budget', 10, 2);
            $table->decimal('spent', 10, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('target_audience')->nullable();
            $table->enum('status', ['draft', 'active', 'paused', 'completed'])->default('draft');
            $table->json('metrics')->nullable(); // Store campaign metrics
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertising_campaigns');
    }
};
