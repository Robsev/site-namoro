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
        Schema::table('user_profiles', function (Blueprint $table) {
            // Children
            $table->enum('has_children', ['yes', 'no', 'prefer_not_to_say'])->nullable()->after('drinking');
            $table->enum('wants_children', ['yes', 'no', 'maybe', 'prefer_not_to_say'])->nullable()->after('has_children');
            
            // Physical characteristics
            $table->enum('body_type', ['slim', 'athletic', 'average', 'curvy', 'plus_size', 'muscular', 'prefer_not_to_say'])->nullable()->after('wants_children');
            $table->integer('height')->nullable()->comment('Height in centimeters')->after('body_type');
            $table->integer('weight')->nullable()->comment('Weight in kilograms')->after('height');
            
            // Lifestyle
            $table->enum('diet_type', ['omnivore', 'vegetarian', 'vegan', 'pescatarian', 'keto', 'paleo', 'other', 'prefer_not_to_say'])->nullable()->after('weight');
            
            // Note: exercise_frequency already exists from a previous migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'has_children',
                'wants_children',
                'body_type',
                'height',
                'weight',
                'diet_type',
            ]);
        });
    }
};
