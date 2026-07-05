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
        // Track credits used in current billing cycle + when it resets
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('featured_credits_used')->default(0)->after('plan_expires_at');
            $table->timestamp('featured_credits_reset_at')->nullable()->after('featured_credits_used');
        });

        // Audit log: which listing was featured, when, by which user
        Schema::create('featured_credit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->timestamp('featured_at')->useCurrent();
            $table->timestamp('unfeatured_at')->nullable();

            $table->index(['user_id', 'featured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_credit_logs');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['featured_credits_used', 'featured_credits_reset_at']);
        });
    }
};
