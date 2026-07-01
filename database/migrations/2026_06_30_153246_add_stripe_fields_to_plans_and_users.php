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
        // Stripe Price ID on each plan (set by admin)
        Schema::table('plans', function (Blueprint $table) {
            $table->string('stripe_price_id')->nullable()->after('price');
        });

        // Stripe customer + subscription tracking on users
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('plan_expires_at');
            $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            $table->string('subscription_status')->nullable()->after('stripe_subscription_id'); // active, canceled, past_due
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('stripe_price_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['stripe_customer_id', 'stripe_subscription_id', 'subscription_status']);
        });
    }
};
