<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rental_unit_id')->constrained('rental_units')->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('phone', 30);
            $table->string('email', 191)->nullable();
            $table->date('preferred_move_in')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'contacted', 'confirmed', 'declined'])->default('new');
            $table->timestamps();

            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_bookings');
    }
};
