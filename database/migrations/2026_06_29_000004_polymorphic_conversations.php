<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Add polymorphic columns
            $table->string('conversable_type')->nullable()->after('id');
            $table->unsignedBigInteger('conversable_id')->nullable()->after('conversable_type');
            $table->index(['conversable_type', 'conversable_id']);
        });

        // Migrate existing listing conversations
        DB::table('conversations')->whereNotNull('listing_id')->update([
            'conversable_type' => 'App\\Models\\Listing',
            'conversable_id'   => DB::raw('listing_id'),
        ]);

        Schema::table('conversations', function (Blueprint $table) {
            // Drop foreign key first, then unique index, then column
            $table->dropForeign(['listing_id']);
            $table->dropUnique(['listing_id', 'buyer_id']);
            $table->dropColumn('listing_id');
            // New unique constraint
            $table->unique(['conversable_type', 'conversable_id', 'buyer_id'], 'conv_unique');
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->foreignId('listing_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unique(['listing_id', 'buyer_id']);
            $table->dropIndex(['conversable_type', 'conversable_id']);
            $table->dropUnique('conv_unique');
            $table->dropColumn(['conversable_type', 'conversable_id']);
        });
    }
};
