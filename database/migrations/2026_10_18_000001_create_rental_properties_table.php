<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_properties', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->string('name');                        // e.g. "Kiritiri Court"
            $table->string('town')->nullable();            // e.g. "Embu"
            $table->string('estate')->nullable();          // e.g. "Kiritiri"
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'town']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_properties');
    }
};
