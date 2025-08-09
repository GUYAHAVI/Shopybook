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
        Schema::table('customers', function (Blueprint $table) {
            // Remove the unique constraint from email
            $table->dropUnique(['email']);
            
            // Add a composite unique constraint on business_id and email
            // This ensures the same email can exist for different businesses
            $table->unique(['business_id', 'email'], 'customers_business_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Remove the composite unique constraint
            $table->dropUnique('customers_business_email_unique');
            
            // Restore the original unique constraint on email
            $table->unique(['email']);
        });
    }
};
