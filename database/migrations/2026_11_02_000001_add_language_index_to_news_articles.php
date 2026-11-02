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
        // Speeds up /news?language=... (status + language filter, same sort as the
        // unfiltered page) — one of the two most-hit query shapes alongside the plain
        // status+is_featured+pub_date one added in the previous news_articles migration.
        Schema::table('news_articles', function (Blueprint $table) {
            $table->index(['status', 'language', 'is_featured', 'pub_date'], 'news_articles_status_lang_featured_pubdate_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_articles', function (Blueprint $table) {
            $table->dropIndex('news_articles_status_lang_featured_pubdate_index');
        });
    }
};
