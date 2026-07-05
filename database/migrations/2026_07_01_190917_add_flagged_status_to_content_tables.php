<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'flagged' to listings.status only if it is not already present.
        // This guard makes the migration safe on both existing databases (partial run)
        // and fresh deployments where 'flagged' was never added.
        $listingsEnumType = DB::select(
            "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'listings' AND COLUMN_NAME = 'status'"
        )[0]->COLUMN_TYPE ?? '';

        if (!str_contains($listingsEnumType, "'flagged'")) {
            DB::statement("ALTER TABLE listings MODIFY COLUMN status ENUM('pending','active','rejected','expired','flagged') NOT NULL DEFAULT 'pending'");
        }

        DB::statement("ALTER TABLE job_listings MODIFY COLUMN status ENUM('draft','active','closed','flagged') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('draft','active','cancelled','completed','flagged') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE businesses MODIFY COLUMN status ENUM('active','inactive','pending','flagged') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE business_posts MODIFY COLUMN status ENUM('pending','active','rejected','expired','flagged') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE listings MODIFY COLUMN status ENUM('pending','active','rejected','expired') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE job_listings MODIFY COLUMN status ENUM('draft','active','closed') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('draft','active','cancelled','completed') NOT NULL DEFAULT 'draft'");
        DB::statement("ALTER TABLE businesses MODIFY COLUMN status ENUM('active','inactive','pending') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE business_posts MODIFY COLUMN status ENUM('pending','active','rejected','expired') NOT NULL DEFAULT 'active'");
    }
};
