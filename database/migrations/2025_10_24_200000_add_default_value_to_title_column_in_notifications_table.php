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
        Schema::table('notifications', function (Blueprint $table) {
            // Modify title column to have a default value
            $table->string('title')->default('Notificação')->change();
            // Modify message column to have a default value
            $table->text('message')->default('Nova notificação')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Remove default values
            $table->string('title')->nullable()->change();
            $table->text('message')->nullable()->change();
        });
    }
};
