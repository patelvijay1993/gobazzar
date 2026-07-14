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
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('postal_code', 10)->nullable()->after('province');
        });
        Schema::table('listings', function (Blueprint $table) {
            $table->string('postal_code', 10)->nullable()->after('province');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->string('postal_code', 10)->nullable()->after('province');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', fn ($t) => $t->dropColumn('postal_code'));
        Schema::table('listings',   fn ($t) => $t->dropColumn('postal_code'));
        Schema::table('events',     fn ($t) => $t->dropColumn('postal_code'));
    }
};
