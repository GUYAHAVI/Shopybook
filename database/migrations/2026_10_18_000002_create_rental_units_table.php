<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_units', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->foreignId('rental_property_id')->nullable()
                  ->constrained('rental_properties')->nullOnDelete();
            $table->string('label');                       // e.g. "House 4", "A3", "Bedsitter 2"
            $table->decimal('rent_amount', 12, 2);
            $table->unsignedTinyInteger('bedrooms')->nullable(); // null = bedsitter/single
            $table->string('unit_type')->nullable();       // house|bedsitter|shop|office|airbnb…
            $table->text('description')->nullable();
            $table->json('images')->nullable();            // storage paths
            $table->enum('status', ['vacant', 'occupied', 'maintenance'])->default('vacant');
            $table->boolean('is_listed')->default(false);  // shown on public /houses search
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'status']);
            $table->index(['status', 'is_listed']);        // public listing query
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_units');
    }
};
