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
        // AI-generated SEO title/description, used in <title>/meta description/OG tags
        // in preference to the raw template fallback built from the post's own title/
        // description. Null = not generated yet (falls back to the existing template);
        // an admin/user-written value here is never overwritten by the auto-generator.
        foreach (['listings', 'job_listings', 'events', 'businesses'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('seo_title', 70)->nullable()->after('fb_post_id');
                $t->string('seo_description', 170)->nullable()->after('seo_title');
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
                $t->dropColumn(['seo_title', 'seo_description']);
            });
        }
    }
};
