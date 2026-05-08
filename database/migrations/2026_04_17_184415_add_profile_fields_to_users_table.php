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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('city')->nullable()->after('avatar');
            $table->string('province')->nullable()->after('city');
            $table->text('bio')->nullable()->after('province');
            $table->boolean('is_admin')->default(false)->after('bio');
            $table->boolean('is_active')->default(true)->after('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone','avatar','city','province','bio','is_admin','is_active']);
        });
    }
};
