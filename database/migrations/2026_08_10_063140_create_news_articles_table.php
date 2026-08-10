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
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('article_id')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->text('link')->nullable();
            $table->text('image_url')->nullable();
            $table->text('video_url')->nullable();
            $table->json('category')->nullable();
            $table->json('keywords')->nullable();
            $table->string('language')->nullable();
            $table->json('country')->nullable();
            $table->string('source_id')->nullable();
            $table->string('source_name')->nullable();
            $table->text('source_icon')->nullable();
            $table->text('source_url')->nullable();
            $table->timestamp('pub_date')->nullable();
            $table->enum('status', ['published', 'hidden'])->default('published');
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();

            $table->index('pub_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
