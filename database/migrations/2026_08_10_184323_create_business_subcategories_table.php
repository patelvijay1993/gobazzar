<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcategory_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['business_id', 'subcategory_id']);
        });

        // Backfill: carry each business's existing single subcategory_id into the pivot.
        DB::table('businesses')
            ->whereNotNull('subcategory_id')
            ->select('id', 'subcategory_id')
            ->orderBy('id')
            ->each(function ($business) {
                DB::table('business_subcategories')->insert([
                    'business_id'    => $business->id,
                    'subcategory_id' => $business->subcategory_id,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_subcategories');
    }
};
