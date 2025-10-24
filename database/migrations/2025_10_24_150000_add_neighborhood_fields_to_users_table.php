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
        Schema::table('users', function (Blueprint $table) {
            $table->string('neighborhood')->nullable();
            $table->string('district')->nullable();
            $table->string('county')->nullable();
            $table->string('road')->nullable();
            $table->string('house_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'neighborhood',
                'district', 
                'county',
                'road',
                'house_number'
            ]);
        });
    }
};
