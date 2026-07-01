<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedTinyInteger('max_images')->default(3)->after('max_listings');
        });

        // Free: 3 photos, Verified: 5 photos, Power Seller: 10 photos
        DB::table('plans')->where('slug', 'free')->update(['max_images' => 3]);
        DB::table('plans')->where('slug', 'verified')->update(['max_images' => 5]);
        DB::table('plans')->where('slug', 'power_seller')->update(['max_images' => 10]);
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('max_images');
        });
    }
};
