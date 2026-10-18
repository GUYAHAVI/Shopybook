<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 'reversed' lets an owner un-mark a rent payment without deleting the
     * audit trail; 'failed' covers terminal STK outcomes.
     */
    public function up(): void
    {
        Schema::table('rental_payments', function (Blueprint $table) {
            $table->enum('status', ['paid', 'pending', 'failed', 'reversed'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('rental_payments', function (Blueprint $table) {
            $table->enum('status', ['paid', 'pending'])->default('pending')->change();
        });
    }
};
