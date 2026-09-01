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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt')->nullable();
            $table->longText('body');
            $table->enum('type', ['info', 'success', 'warning', 'urgent'])->default('info');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_pinned')->default(false);
            $table->string('link_url')->nullable();
            $table->string('link_text')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('is_pinned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
