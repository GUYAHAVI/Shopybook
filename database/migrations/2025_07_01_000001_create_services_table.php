<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('duration')->nullable(); // in minutes
            $table->decimal('commission_rate', 5, 2)->nullable(); // percent
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('services');
    }
}; 