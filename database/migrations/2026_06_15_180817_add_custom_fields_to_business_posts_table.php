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
        Schema::table('business_posts', function (Blueprint $table) {
            // { "cuisine_type": "Veg", "seating": "40" }
            $table->json('custom_fields')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('business_posts', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
    }
};
