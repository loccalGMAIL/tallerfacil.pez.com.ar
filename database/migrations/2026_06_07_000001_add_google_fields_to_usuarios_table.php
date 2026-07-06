<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            $table->string('google_id', 100)->nullable()->unique()->after('password');
            $table->string('google_avatar', 255)->nullable()->after('google_id');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'google_avatar']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
