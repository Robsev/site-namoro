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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->text('interests')->nullable(); // JSON array of interests
            $table->text('hobbies')->nullable(); // JSON array of hobbies
            $table->text('personality_traits')->nullable(); // JSON array for psychological matching
            $table->enum('relationship_goal', ['friendship', 'romance', 'casual', 'serious', 'marriage'])->nullable();
            $table->enum('education_level', ['high_school', 'bachelor', 'master', 'phd', 'other'])->nullable();
            $table->string('occupation')->nullable();
            $table->enum('smoking', ['never', 'occasionally', 'regularly', 'prefer_not_to_say'])->nullable();
            $table->enum('drinking', ['never', 'occasionally', 'regularly', 'prefer_not_to_say'])->nullable();
            $table->enum('exercise_frequency', ['never', 'rarely', 'weekly', 'daily'])->nullable();
            $table->text('looking_for')->nullable(); // Description of what they're looking for
            $table->integer('age_min')->nullable(); // Minimum age preference
            $table->integer('age_max')->nullable(); // Maximum age preference
            $table->integer('max_distance')->nullable(); // Maximum distance in km
            $table->boolean('show_distance')->default(true);
            $table->boolean('show_age')->default(true);
            $table->boolean('show_online_status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
