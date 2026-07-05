<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 80);           // viewed_listing, searched, registered, login, etc.
            $table->string('subject_type', 100)->nullable(); // App\Models\Listing etc.
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_label', 200)->nullable(); // title/name at time of action
            $table->string('url', 500)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('device', 20)->nullable();
            $table->json('meta')->nullable();       // extra data (search keyword, province, etc.)
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['ip', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
