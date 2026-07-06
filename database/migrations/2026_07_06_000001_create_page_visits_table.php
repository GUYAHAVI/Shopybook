<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('business_id')->nullable();
            $table->string('path', 500);
            $table->string('route_name')->nullable();
            $table->string('method', 10)->default('GET');
            $table->unsignedInteger('status_code')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('session_id', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['route_name', 'created_at']);
            $table->index('created_at');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};
