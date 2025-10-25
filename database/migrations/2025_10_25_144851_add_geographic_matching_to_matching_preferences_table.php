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
            // Add geographic matching toggle
            $table->boolean('enable_geographic_matching')->default(true)->after('max_distance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matching_preferences', function (Blueprint $table) {
            $table->dropColumn('enable_geographic_matching');
        });
    }
};
