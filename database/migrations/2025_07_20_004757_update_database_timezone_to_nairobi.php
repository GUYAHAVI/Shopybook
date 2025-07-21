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
        // Set MySQL session timezone to Africa/Nairobi (+03:00)
        DB::statement("SET time_zone = '+03:00'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to UTC
        DB::statement("SET time_zone = '+00:00'");
    }
};
