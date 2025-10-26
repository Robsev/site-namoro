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
        // Modify the message_type enum to include 'audio'
        \DB::statement("ALTER TABLE messages MODIFY COLUMN message_type ENUM('text', 'image', 'file', 'audio') DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        \DB::statement("ALTER TABLE messages MODIFY COLUMN message_type ENUM('text', 'image', 'file') DEFAULT 'text'");
    }
};
