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
        Schema::create('carpools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            $table->enum('ride_type', ['offer', 'request'])->default('offer'); // offering a ride vs looking for one
            $table->string('from_city');
            $table->string('from_province');
            $table->string('to_city');
            $table->string('to_province');
            $table->dateTime('travel_date');
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_days')->nullable(); // e.g. "Mon,Wed,Fri" for repeat commutes

            $table->unsignedTinyInteger('seats_available')->default(1);
            $table->string('price')->nullable()->default('Free'); // per-seat, string like events.price ("Free" or "$15")

            $table->string('vehicle')->nullable(); // e.g. "Toyota Camry, White"
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();

            $table->json('tags')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('chat_enabled')->default(true);
            $table->enum('status', ['draft', 'active', 'inactive', 'completed', 'flagged'])->default('draft');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('inactive_at')->nullable();
            $table->unsignedInteger('views')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['from_city', 'to_city']);
            $table->index('travel_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carpools');
    }
};
