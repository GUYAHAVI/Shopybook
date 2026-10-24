<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_id')->unique();
            $table->date('ledger_start_date')->nullable();
            $table->string('setup_mode', 20)->nullable(); // backfill | opening
            $table->timestamp('setup_completed_at')->nullable();
            $table->date('lock_date')->nullable(); // books closed up to and including this date
            $table->boolean('vat_registered')->default(false);
            $table->unsignedTinyInteger('fiscal_year_start_month')->default(1);
            $table->json('payment_accounts')->nullable(); // payment method => account_id overrides
            $table->unsignedBigInteger('next_entry_number')->default(1);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });

        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('code', 20);
            $table->string('name');
            $table->string('type', 20); // asset | liability | equity | income | expense
            $table->string('sub_type', 30)->nullable();
            $table->string('system_key', 40)->nullable();
            $table->boolean('is_group')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->unique(['business_id', 'code']);
            $table->unique(['business_id', 'system_key']);
            $table->index(['business_id', 'type']);
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->string('entry_number', 30);
            $table->date('posting_date');
            $table->string('description')->nullable();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('source_event', 40)->default('post');
            $table->foreignId('reverses_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('reversed_by_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->string('status', 10)->default('posted'); // draft | posted
            $table->uuid('batch_id')->nullable();
            $table->decimal('total', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->unique(['business_id', 'entry_number']);
            $table->index(['business_id', 'posting_date']);
            $table->index(['source_type', 'source_id', 'source_event']);
            $table->index('batch_id');
        });

        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->string('business_id');
            $table->foreignId('account_id')->constrained('accounts');
            $table->date('posting_date');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->string('party_type', 30)->nullable(); // customer | supplier | staff | tenant
            $table->string('party_id', 64)->nullable();
            $table->string('memo')->nullable();

            $table->index(['business_id', 'account_id', 'posting_date']);
            $table->index(['business_id', 'party_type', 'party_id']);
        });

        Schema::create('stock_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->unsignedBigInteger('product_id');
            $table->date('posting_date');
            $table->decimal('qty_change', 14, 3);
            $table->decimal('valuation_rate', 15, 4); // moving-average cost after this movement
            $table->decimal('value_change', 15, 2);
            $table->decimal('qty_after', 14, 3);
            $table->decimal('value_after', 15, 2);
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->uuid('batch_id')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['business_id', 'product_id', 'id']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ledger_entries');
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('accounting_settings');
    }
};
