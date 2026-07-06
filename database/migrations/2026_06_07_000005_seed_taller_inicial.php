<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tablas que quedan con taller_id NOT NULL después de poblar
    private array $tablasNotNull = [
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
        // 1. Crear el taller inicial con los datos de negocio_config
        $negocio = DB::table('negocio_config')->where('id', 1)->first();

        $tallerId = DB::table('talleres')->insertGetId([
            'nombre'       => $negocio?->nombre ?? 'TallerFácil',
            'razon_social' => $negocio?->razon_social,
            'cuit'         => $negocio?->cuit,
            'telefono'     => $negocio?->telefono,
            'email'        => $negocio?->email,
            'direccion'    => $negocio?->direccion,
            'horario'      => $negocio?->horario,
            'notas'        => $negocio?->notas,
            'subdominio'   => env('TALLER_INICIAL_SUBDOMINIO', 'app'),
            'activo'       => true,
            'en_prueba'    => false,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // 2. Asignar taller_id a todos los registros existentes
        $todasLasTablas = array_merge(['usuarios'], $this->tablasNotNull);
        foreach ($todasLasTablas as $tabla) {
            DB::table($tabla)->whereNull('taller_id')->update(['taller_id' => $tallerId]);
        }

        // 3. Crear suscripción activa para el taller inicial
        DB::table('suscripciones')->insert([
            'taller_id'  => $tallerId,
            'plan'       => 'basico',
            'estado'     => 'activo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Hacer taller_id NOT NULL en las tablas que corresponde
        foreach ($this->tablasNotNull as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->unsignedBigInteger('taller_id')->nullable(false)->change();
            });
        }

        // 5. Agregar FK constraints
        foreach ($this->tablasNotNull as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->foreign('taller_id')->references('id')->on('talleres')->cascadeOnDelete();
            });
        }

        // usuarios: FK nullable (superadmin tiene taller_id NULL)
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreign('taller_id')->references('id')->on('talleres')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Quitar FK constraints
        $todasLasTablas = array_merge(['usuarios'], $this->tablasNotNull);
        foreach ($todasLasTablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                $table->dropForeign("{$tabla}_taller_id_foreign");
            });
        }

        // Volver taller_id nullable
        foreach ($this->tablasNotNull as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->unsignedBigInteger('taller_id')->nullable()->change();
            });
        }

        // Limpiar datos del seed
        DB::table('suscripciones')->truncate();
        DB::table('talleres')->truncate();
        $todasLasTablas = array_merge(['usuarios'], $this->tablasNotNull);
        foreach ($todasLasTablas as $tabla) {
            DB::table($tabla)->update(['taller_id' => null]);
        }
    }
};
