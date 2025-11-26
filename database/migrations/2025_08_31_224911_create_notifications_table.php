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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->string('type'); // 'order', 'service_booking', 'payment', etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Store additional data like order ID, amounts, etc.
            $table->boolean('read')->default(false);
            $table->string('icon')->nullable(); // Icon class for the notification
            $table->string('color')->default('primary'); // Bootstrap color class
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'read', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};