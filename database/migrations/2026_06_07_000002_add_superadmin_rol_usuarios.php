<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('superadmin','administrador','mecanico') NOT NULL DEFAULT 'mecanico'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('administrador','mecanico') NOT NULL DEFAULT 'mecanico'");
    }
};
