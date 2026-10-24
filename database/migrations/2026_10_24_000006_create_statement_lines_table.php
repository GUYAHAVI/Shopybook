<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statement_lines', function (Blueprint $table) {
            $table->id();
            $table->string('business_id')->index();
            $table->unsignedBigInteger('account_id');
            $table->date('txn_date');
            $table->decimal('amount', 15, 2); // + money in, - money out
            $table->string('reference')->nullable();
            $table->string('memo')->nullable();
            $table->unsignedBigInteger('journal_line_id')->nullable();
            $table->boolean('matched')->default(false);
            $table->timestamps();
            $table->unique(['business_id', 'account_id', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statement_lines');
    }
};
