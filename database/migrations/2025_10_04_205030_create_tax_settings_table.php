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
        Schema::create('tax_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_id');
            $table->boolean('tax_enabled')->default(false);
            $table->string('tax_type')->default('VAT'); // VAT, Sales Tax, GST, etc.
            $table->decimal('tax_rate', 5, 2)->default(16.00); // Default 16% VAT for Kenya
            $table->string('tax_number')->nullable(); // KRA PIN or Tax Registration Number
            $table->string('tax_name')->default('VAT'); // Display name
            $table->boolean('tax_inclusive')->default(false); // Prices include tax or not
            $table->string('tax_period')->default('monthly'); // monthly, quarterly, annual
            $table->text('tax_exemption_items')->nullable(); // JSON array of exempt product IDs
            $table->boolean('show_tax_on_receipt')->default(true);
            $table->boolean('separate_tax_column')->default(true);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->unique('business_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_settings');
    }
};
