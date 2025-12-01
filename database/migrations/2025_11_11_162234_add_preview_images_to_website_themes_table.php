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
        Schema::table('website_themes', function (Blueprint $table) {
            if (!Schema::hasColumn('website_themes', 'preview_image')) {
                $table->string('preview_image')->nullable()->after('description');
            }
            if (!Schema::hasColumn('website_themes', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('preview_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_themes', function (Blueprint $table) {
            $table->dropColumn(['preview_image', 'thumbnail']);
        });
    }
};
