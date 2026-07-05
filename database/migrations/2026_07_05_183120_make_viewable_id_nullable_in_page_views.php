<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            $table->string('viewable_type')->nullable()->change();
            $table->unsignedBigInteger('viewable_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            $table->string('viewable_type')->nullable(false)->change();
            $table->unsignedBigInteger('viewable_id')->nullable(false)->change();
        });
    }
};
