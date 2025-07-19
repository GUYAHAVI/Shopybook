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
        Schema::create('service_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_record_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('staff_id');
            $table->decimal('amount', 10, 2);
            $table->integer('sequence_order')->default(1); // Order of service (1st, 2nd, etc.)
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('service_record_id')->references('id')->on('service_records')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_items');
    }
};
