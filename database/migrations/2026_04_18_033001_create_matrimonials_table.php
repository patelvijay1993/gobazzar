<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matrimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('profile_for'); // self, son, daughter, brother, sister, friend
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('gender'); // male, female
            $table->unsignedTinyInteger('age');
            $table->string('height')->nullable();          // e.g. 5'7"
            $table->string('religion')->nullable();
            $table->string('caste')->nullable();
            $table->string('mother_tongue')->nullable();
            $table->string('education')->nullable();
            $table->string('occupation')->nullable();
            $table->string('income')->nullable();          // annual range
            $table->string('marital_status')->default('never_married'); // never_married, divorced, widowed
            $table->string('diet')->nullable();            // veg, non-veg, eggetarian
            $table->string('city');
            $table->string('province')->nullable();
            $table->string('country')->default('Canada');
            $table->text('about')->nullable();
            $table->text('partner_preference')->nullable();
            $table->string('photo')->nullable();
            $table->json('photos')->nullable();            // gallery
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('hide_contact')->default(false);
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matrimonials');
    }
};
