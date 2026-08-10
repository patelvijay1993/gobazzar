<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = ['listings', 'job_listings', 'events', 'businesses', 'business_posts'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->softDeletes();
            });
        }

        // Add 'inactive' to status enums that don't already have it (Business already does).
        DB::statement("ALTER TABLE listings MODIFY status ENUM('pending','active','inactive','rejected','expired') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE job_listings MODIFY status ENUM('draft','active','inactive','closed') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE events MODIFY status ENUM('draft','active','inactive','cancelled','completed') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE business_posts MODIFY status ENUM('pending','active','inactive','rejected','expired') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropSoftDeletes();
            });
        }

        DB::statement("ALTER TABLE listings MODIFY status ENUM('pending','active','rejected','expired') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE job_listings MODIFY status ENUM('draft','active','closed') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE events MODIFY status ENUM('draft','active','cancelled','completed') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE business_posts MODIFY status ENUM('pending','active','rejected','expired') NOT NULL DEFAULT 'active'");
    }
};
