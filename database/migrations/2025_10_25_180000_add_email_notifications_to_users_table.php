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
            $table->boolean('email_notifications_enabled')->default(true)->after('email_verified_at');
            $table->boolean('email_new_matches')->default(true)->after('email_notifications_enabled');
            $table->boolean('email_new_likes')->default(true)->after('email_new_matches');
            $table->boolean('email_new_messages')->default(false)->after('email_new_likes');
            $table->boolean('email_photo_approvals')->default(true)->after('email_new_messages');
            $table->boolean('email_marketing')->default(false)->after('email_photo_approvals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_notifications_enabled',
                'email_new_matches',
                'email_new_likes',
                'email_new_messages',
                'email_photo_approvals',
                'email_marketing'
            ]);
        });
    }
};
