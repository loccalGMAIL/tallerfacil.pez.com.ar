<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tablas que requieren taller_id
    private array $tablas = [
        'usuarios',
        'clientes',
        'vehiculos',
        'ordenes',
        'servicios',
        'wa_config',
        'wa_plantillas',
        'wa_mensajes',
        'wa_mensajes_guardados',
        'wa_recordatorio_config',
    ];

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                $table->unsignedBigInteger('taller_id')->nullable()->after('id');
                $table->index('taller_id');
            });
        }

        // El email de usuarios debe ser único por taller, no globalmente
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropUnique('usuarios_email_unique');
            $table->unique(['email', 'taller_id']);
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropUnique(['email', 'taller_id']);
            $table->unique('email');
        });

        foreach (array_reverse($this->tablas) as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropIndex(['taller_id']);
                $table->dropColumn('taller_id');
            });
        }
    }
};
