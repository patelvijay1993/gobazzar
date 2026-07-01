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
        Schema::create('payment_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('stripe_payment_intent_id')->nullable()->index();
            $table->string('stripe_invoice_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->string('plan_slug');
            $table->string('plan_name');
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('usd');
            $table->string('status'); // paid, failed, refunded
            $table->string('description')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_history');
    }
};
