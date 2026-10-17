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
        // Null = never posted to the GoBazaar Facebook Page yet (or the attempt failed).
        // Set to the Graph API post id once it's posted, so we never double-post the same
        // listing/job/event/business when it's saved again.
        foreach (['listings', 'job_listings', 'events', 'businesses'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('fb_post_id')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['listings', 'job_listings', 'events', 'businesses'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('fb_post_id');
            });
        }
    }
};
