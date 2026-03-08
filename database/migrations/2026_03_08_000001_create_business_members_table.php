<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_members', function (Blueprint $table) {
            $table->id();
            $table->string('business_id'); // matches string PK on businesses
            $table->unsignedBigInteger('user_id')->nullable(); // null until they accept invite
            $table->string('invited_email');
            $table->string('name');
            $table->enum('role', ['admin', 'manager', 'cashier', 'staff', 'viewer'])->default('staff');
            $table->json('permissions')->nullable(); // array of module slugs
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->unsignedBigInteger('invited_by'); // owner user id
            $table->string('invite_token')->nullable()->unique();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->unique(['business_id', 'invited_email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_members');
    }
};
