<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->morphs('reportable'); // listing, event, business, job, blog_post
            $table->enum('reason', [
                'pornography',
                'harmful',
                'misleading',
                'spam',
                'fake',
                'other',
            ]);
            $table->text('details')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'actioned', 'dismissed'])->default('pending');
            $table->string('reporter_ip', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
