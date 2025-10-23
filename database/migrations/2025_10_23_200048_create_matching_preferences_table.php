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
        Schema::create('matching_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('preferred_genders')->nullable(); // Array of preferred genders
            $table->integer('min_age')->default(18);
            $table->integer('max_age')->default(100);
            $table->integer('max_distance')->default(50); // in kilometers
            $table->json('preferred_interests')->nullable(); // Array of preferred interests
            $table->json('preferred_personality_traits')->nullable(); // Array for psychological matching
            $table->json('preferred_education_levels')->nullable();
            $table->json('preferred_relationship_goals')->nullable();
            $table->boolean('smoking_ok')->default(true);
            $table->boolean('drinking_ok')->default(true);
            $table->boolean('online_only')->default(false);
            $table->boolean('verified_only')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matching_preferences');
    }
};
