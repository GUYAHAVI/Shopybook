<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->unsignedBigInteger('user_id');
            $table->string('module'); // e.g. 'pos', 'products', 'orders'
            $table->string('action'); // e.g. 'created', 'updated', 'deleted', 'viewed'
            $table->string('description'); // human-readable
            $table->json('meta')->nullable(); // extra data (record ID, old/new values)
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
