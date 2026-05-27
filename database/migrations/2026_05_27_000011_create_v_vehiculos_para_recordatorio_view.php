<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW v_vehiculos_para_recordatorio AS
            SELECT
                v.id            AS vehiculo_id,
                v.patente,
                v.marca,
                v.modelo,
                v.anio,
                v.km_actual,
                v.km_ultimo_service,
                v.fecha_ultimo_service,
                c.id            AS cliente_id,
                c.nombre,
                c.telefono_normalizado AS telefono,
                c.email,
                TIMESTAMPDIFF(MONTH, v.fecha_ultimo_service, CURDATE()) AS meses_desde_service,
                (v.km_actual - v.km_ultimo_service) AS km_desde_service,
                CASE
                    WHEN v.fecha_ultimo_service IS NOT NULL
                         AND TIMESTAMPDIFF(MONTH, v.fecha_ultimo_service, CURDATE())
                             >= (SELECT umbral_meses FROM wa_recordatorio_config WHERE id = 1)
                         THEN 'service_fecha'
                    WHEN v.km_ultimo_service IS NOT NULL AND v.km_actual IS NOT NULL
                         AND (v.km_actual - v.km_ultimo_service)
                             >= (SELECT umbral_km FROM wa_recordatorio_config WHERE id = 1)
                         THEN 'service_km'
                END AS tipo_recordatorio
            FROM vehiculos v
            JOIN clientes c ON v.cliente_id = c.id
            WHERE
                v.activo = 1
                AND c.activo = 1
                AND (SELECT activo FROM wa_recordatorio_config WHERE id = 1) = 1
                AND (
                    (v.fecha_ultimo_service IS NOT NULL
                     AND TIMESTAMPDIFF(MONTH, v.fecha_ultimo_service, CURDATE())
                         >= (SELECT umbral_meses FROM wa_recordatorio_config WHERE id = 1))
                    OR
                    (v.km_ultimo_service IS NOT NULL AND v.km_actual IS NOT NULL
                     AND (v.km_actual - v.km_ultimo_service)
                         >= (SELECT umbral_km FROM wa_recordatorio_config WHERE id = 1))
                )
                AND v.id NOT IN (
                    SELECT vehiculo_id
                    FROM wa_mensajes
                    WHERE tipo = 'recordatorio'
                      AND estado_entrega IN ('enviado', 'entregado', 'leido')
                      AND fecha_hora >= DATE_SUB(NOW(), INTERVAL
                          (SELECT ventana_minima_dias FROM wa_recordatorio_config WHERE id = 1) DAY)
                )
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_vehiculos_para_recordatorio');
    }
};
