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
        Schema::create('advertise_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('business_name')->nullable();
            $table->string('website')->nullable();
            $table->string('ad_position')->nullable(); // home-banner|sidebar|inline
            $table->string('budget')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new'); // new|contacted|closed
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertise_requests');
    }
};
