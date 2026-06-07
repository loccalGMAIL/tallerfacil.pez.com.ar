<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_plantillas', function (Blueprint $table) {
            // El tipo pasa a ser único por taller, no globalmente
            $table->dropUnique('wa_plantillas_tipo_unique');
            $table->unique(['tipo', 'taller_id']);
        });
    }

    public function down(): void
    {
        Schema::table('wa_plantillas', function (Blueprint $table) {
            $table->dropUnique(['tipo', 'taller_id']);
            $table->unique('tipo');
        });
    }
};
