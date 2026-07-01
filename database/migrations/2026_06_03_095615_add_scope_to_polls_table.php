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
        Schema::table('polls', function (Blueprint $table) {
            // 'canada' | 'province' | 'city'
            $table->string('scope')->default('canada')->after('sort_order');
            $table->string('province')->nullable()->after('scope');
            $table->string('city')->nullable()->after('province');
        });
    }

    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn(['scope', 'province', 'city']);
        });
    }
};
