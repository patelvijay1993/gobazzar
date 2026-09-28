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
        Schema::table('businesses', function (Blueprint $table) {
            // Null = not yet claimed by a real owner (e.g. imported from a Lead, still assigned
            // to the admin account). Set once a business owner successfully claims the listing.
            $table->timestamp('claimed_at')->nullable()->after('user_id');
            $table->timestamp('claim_email_sent_at')->nullable()->after('claimed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['claimed_at', 'claim_email_sent_at']);
        });
    }
};
