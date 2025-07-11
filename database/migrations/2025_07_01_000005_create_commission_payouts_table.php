<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('commission_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->unsignedBigInteger('staff_id');
            $table->decimal('amount', 10, 2);
            $table->dateTime('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('commission_payouts');
    }
}; 