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
        // Without this, the default /news listing (status='published' ordered by
        // is_featured desc, pub_date desc) did a full scan + filesort over every
        // published row — at 160k+ rows this alone was enough to exhaust PHP-FPM
        // under normal/bot traffic and take the whole site down with 502s.
        if (!Schema::hasIndex('news_articles', 'news_articles_status_featured_pubdate_index')) {
            Schema::table('news_articles', function (Blueprint $table) {
                $table->index(['status', 'is_featured', 'pub_date'], 'news_articles_status_featured_pubdate_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_articles', function (Blueprint $table) {
            $table->dropIndex('news_articles_status_featured_pubdate_index');
        });
    }
};
