<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WaPlantillasSeeder extends Seeder
{
    public function run(): void
    {
        $plantillas = [
            [
                'tipo'       => 'presupuesto',
                'texto'      => "Hola {nombre} 👋\n\nTe enviamos el presupuesto para tu *{marca} {modelo}* (patente *{patente}*):\n\n{items_lista}\n\n*Total estimado: \${total}*\n\nCualquier consulta estamos a disposición. ¡Saludos del equipo del taller! 🔧",
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo'       => 'recepcion',
                'texto'      => "Hola {nombre} 👋\n\nConfirmamos que recibimos tu *{marca} {modelo}* (patente *{patente}*).\n\n📋 Orden N°: *{numero_orden}*\n📅 Fecha de ingreso: *{fecha_ingreso}*\n\nTe avisaremos cuando esté listo. ¡Gracias por confiar en nosotros! 🔧",
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo'       => 'recordatorio',
                'texto'      => "Hola {nombre} 👋\n\nTe recordamos que tu *{marca} {modelo}* (patente *{patente}*) podría necesitar su próximo service.\n\n¿Lo agendamos? Escribinos y coordinamos una fecha. 🔧\n\n¡Saludos!",
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($plantillas as $plantilla) {
            DB::table('wa_plantillas')->insertOrIgnore($plantilla);
        }
    }
}
