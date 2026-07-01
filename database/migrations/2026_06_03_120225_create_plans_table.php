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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();           // free|basic|premium|business
            $table->string('name');                     // Free, Basic, Premium, Business
            $table->string('icon')->default('🆓');      // emoji
            $table->string('icon_bg')->default('#f0ede8'); // icon bg color
            $table->decimal('price', 8, 2)->default(0);
            $table->string('period')->default('forever'); // forever|month|year
            $table->string('tagline')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            // Features — stored as JSON array of {text, included, highlight}
            $table->json('features')->nullable();
            // Limits
            $table->unsignedInteger('post_days')->default(7);      // post visibility days, 0=permanent
            $table->unsignedInteger('biz_listings')->default(0);   // business directory listings allowed
            $table->boolean('featured_placement')->default(false);
            $table->boolean('unlimited_posts')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->boolean('analytics')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
