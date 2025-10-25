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
        Schema::table('matching_preferences', function (Blueprint $table) {
            // Add photos only filter
            $table->boolean('photos_only')->default(false)->after('verified_only');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matching_preferences', function (Blueprint $table) {
            $table->dropColumn('photos_only');
        });
    }
};
