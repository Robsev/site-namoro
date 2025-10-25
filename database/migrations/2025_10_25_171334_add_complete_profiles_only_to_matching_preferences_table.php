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
            // Add complete profiles only filter
            $table->boolean('complete_profiles_only')->default(false)->after('photos_only');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matching_preferences', function (Blueprint $table) {
            $table->dropColumn('complete_profiles_only');
        });
    }
};
