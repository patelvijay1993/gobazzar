<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix businesses.hours column type mismatch.
     *
     * Column was TEXT but Business model casts it as 'array' (JSON).
     * Legacy rows contain plain-text strings that json_decode returns null for,
     * causing blank hours display on all business profiles.
     *
     * Step 1: JSON-encode plain-text rows into {"note":"..."} so they are valid JSON.
     * Step 2: ALTER column from TEXT to JSON NULL.
     *
     * Rows already containing valid JSON (id=43) are left untouched.
     * NULL rows remain NULL.
     */
    public function up(): void
    {
        // Step 1 — migrate plain-text hours rows to {"note":"..."} JSON structure
        $rows = DB::table('businesses')->whereNotNull('hours')->get(['id', 'hours']);

        foreach ($rows as $row) {
            $decoded = json_decode($row->hours, true);
            $alreadyJson = json_last_error() === JSON_ERROR_NONE && $decoded !== null;

            if (!$alreadyJson) {
                DB::table('businesses')
                    ->where('id', $row->id)
                    ->update(['hours' => json_encode(['note' => $row->hours])]);
            }
        }

        // Step 2 — change column type from TEXT to JSON
        DB::statement('ALTER TABLE businesses MODIFY COLUMN hours JSON NULL');
    }

    public function down(): void
    {
        // Revert JSON column back to TEXT — stored JSON strings remain valid as TEXT
        DB::statement('ALTER TABLE businesses MODIFY COLUMN hours TEXT NULL');
    }
};
