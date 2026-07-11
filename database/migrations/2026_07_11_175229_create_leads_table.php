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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->integer('review_count')->nullable();
            $table->string('google_place_id')->nullable()->unique();
            $table->string('google_maps_url')->nullable();
            $table->enum('status', ['new', 'contacted', 'interested', 'not_interested', 'converted'])->default('new');
            $table->enum('contact_method', ['none', 'email', 'whatsapp', 'both'])->default('none');
            $table->text('notes')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->string('source')->default('google_maps');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
