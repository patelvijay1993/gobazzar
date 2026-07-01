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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');                          // stored path
            $table->string('click_url');
            // position: home-banner | sidebar | inline
            $table->string('position')->default('sidebar');
            // scope: canada | province | city
            $table->string('scope')->default('canada');
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['position', 'is_active']);
            $table->index(['scope', 'province', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
