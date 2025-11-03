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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Remove Stripe fields
            $table->dropColumn(['stripe_subscription_id', 'stripe_customer_id']);
            
            // Add CommerceGate fields
            $table->string('commercegate_subscription_id')->nullable()->after('user_id');
            $table->string('commercegate_transaction_id')->nullable()->after('commercegate_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Restore Stripe fields
            $table->string('stripe_subscription_id')->nullable()->after('user_id');
            $table->string('stripe_customer_id')->nullable()->after('stripe_subscription_id');
            
            // Remove CommerceGate fields
            $table->dropColumn(['commercegate_subscription_id', 'commercegate_transaction_id']);
        });
    }
};

