<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->enum('type', ['utility', 'product', 'rent', 'water', 'misc', 'activity', 'renovation', 'other']);
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('costs');
    }
}; 