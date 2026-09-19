<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('end_date');
        });

        // Backfill: an event "expires" when it ends — end_date if set, else start_date.
        DB::statement("UPDATE events SET expires_at = COALESCE(end_date, start_date) WHERE expires_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
