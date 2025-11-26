<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make transaction_id nullable and remove unique constraint
        DB::statement('ALTER TABLE payments MODIFY transaction_id VARCHAR(255) NULL');
        
        // Drop unique constraint if it exists
        try {
            DB::statement('ALTER TABLE payments DROP INDEX payments_transaction_id_unique');
        } catch (\Exception $e) {
            // Index might not exist or have different name
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE payments MODIFY transaction_id VARCHAR(255) NOT NULL');
    }
};
