<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_tenants', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->foreignId('rental_unit_id')->nullable()
                  ->constrained('rental_units')->nullOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('id_number')->nullable();       // national ID / passport
            $table->decimal('rent_amount', 12, 2);         // rent agreed at move-in
            $table->enum('status', ['active', 'vacated'])->default('active');
            $table->date('move_in_date')->nullable();
            $table->date('move_out_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_tenants');
    }
};
