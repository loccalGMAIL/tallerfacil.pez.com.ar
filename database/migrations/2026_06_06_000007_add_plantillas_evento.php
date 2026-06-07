<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ampliar enums (MySQL)
        DB::statement("ALTER TABLE wa_plantillas MODIFY COLUMN tipo ENUM('presupuesto','recepcion','recordatorio','listo','reparacion','entregado') NOT NULL");
        DB::statement("ALTER TABLE wa_mensajes MODIFY COLUMN tipo ENUM('presupuesto','recepcion','recordatorio','listo','reparacion','entregado','manual') NOT NULL");

        // Insertar plantillas de evento nuevas (idempotente)
        $nuevas = [
            'reparacion' => "Hola {nombre} 👋\n\nYa empezamos a trabajar en tu *{marca} {modelo}* (patente *{patente}*).\n\n📋 Orden N°: *{numero_orden}*\n\nTe avisamos apenas esté listo. ¡Saludos! 🔧",
            'listo'      => "¡Hola {nombre}! 🎉\n\nTu *{marca} {modelo}* (patente *{patente}*) ya está *listo para retirar*.\n\n📋 Orden N°: *{numero_orden}*\n💰 Total: *\${total}*\n\nTe esperamos. ¡Gracias por confiar en nosotros! 🔧",
            'entregado'  => "Hola {nombre} 🙌\n\n¡Gracias por elegirnos para el service de tu *{marca} {modelo}*!\n\nCualquier cosa que necesites, estamos a disposición. Si te gustó la atención, nos ayudaría muchísimo que nos recomiendes. ¡Buenos kilómetros! 🚗",
        ];

        foreach ($nuevas as $tipo => $texto) {
            $existe = DB::table('wa_plantillas')->where('tipo', $tipo)->exists();
            if (!$existe) {
                DB::table('wa_plantillas')->insert([
                    'tipo'       => $tipo,
                    'texto'      => $texto,
                    'activo'     => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('wa_plantillas')->whereIn('tipo', ['listo', 'reparacion', 'entregado'])->delete();
        DB::statement("ALTER TABLE wa_plantillas MODIFY COLUMN tipo ENUM('presupuesto','recepcion','recordatorio') NOT NULL");
        DB::statement("ALTER TABLE wa_mensajes MODIFY COLUMN tipo ENUM('presupuesto','recepcion','recordatorio') NOT NULL");
    }
};
