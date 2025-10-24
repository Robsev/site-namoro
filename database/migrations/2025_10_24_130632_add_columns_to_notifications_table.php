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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // match, message, like, super_like, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data (user_id, match_id, etc.)
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            // Indexes for better performance
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_read']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'type',
                'title',
                'message',
                'data',
                'is_read',
                'read_at'
            ]);
        });
    }
};
