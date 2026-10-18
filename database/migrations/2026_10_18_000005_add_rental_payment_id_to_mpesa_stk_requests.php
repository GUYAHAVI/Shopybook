<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mpesa_stk_requests', function (Blueprint $table) {
            $table->foreignId('rental_payment_id')->nullable()->after('order_id')
                  ->constrained('rental_payments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mpesa_stk_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rental_payment_id');
        });
    }
};
