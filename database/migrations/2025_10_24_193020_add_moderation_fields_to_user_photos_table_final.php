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
        Schema::table('user_photos', function (Blueprint $table) {
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('moderated_at')->nullable();
            $table->string('moderation_status')->default('pending'); // pending, approved, rejected
            $table->text('moderation_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_photos', function (Blueprint $table) {
            $table->dropColumn([
                'moderated_by',
                'moderated_at',
                'moderation_status',
                'moderation_notes',
            ]);
        });
    }
};
