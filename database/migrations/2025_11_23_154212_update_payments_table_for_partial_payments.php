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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'business_id')) {
                $table->string('business_id')->nullable()->after('order_id');
                $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            }
            if (!Schema::hasColumn('payments', 'payment_number')) {
                $table->string('payment_number')->nullable()->unique()->after('business_id');
            }
            if (!Schema::hasColumn('payments', 'transaction_reference')) {
                $table->string('transaction_reference')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('payments', 'received_by')) {
                $table->foreignId('received_by')->nullable()->after('notes')->constrained('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $columns = ['business_id', 'payment_number', 'transaction_reference', 'received_by'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
