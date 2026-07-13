<?php

namespace App\Http\Controllers;

use App\Models\WaConfig;
use App\Models\WaMensaje;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function evolution(Request $request): JsonResponse
    {
        // Response 200 inmediato — no hacer trabajo lento aquí
        $payload = $request->all();

        try {
            $event = data_get($payload, 'event') ?? data_get($payload, 'type');

            if ($event === 'messages.update' || $event === 'message.ack') {
                $this->procesarActualizacion($payload);
            }

            // Eventos que alimentan el portal admin (tablas portal_*)
            if ($event === 'messages.upsert') {
                $this->contarRecibidos($payload);
            }

            if ($event === 'connection.update') {
                $this->registrarCambioConexion($payload);
            }
        } catch (\Throwable $e) {
            Log::warning('Webhook Evolution error: ' . $e->getMessage(), ['payload' => $payload]);
        }

        return response()->json(['ok' => true]);
    }

    private function procesarActualizacion(array $payload): void
    {
        $updates = data_get($payload, 'data', []);
        if (!is_array($updates) || empty($updates)) {
            $updates = [$payload];
        }

        foreach ($updates as $update) {
            $messageId = data_get($update, 'key.id') ?? data_get($update, 'id');

            // Evolution API envía status como string ("DELIVERY_ACK", "READ", "ERROR")
            // o como número ACK Baileys (2, 3, -1)
            $ackStr = data_get($update, 'update.status')
                   ?? data_get($update, 'update.message.ackStatus')
                   ?? data_get($update, 'status')
                   ?? data_get($update, 'ack');

            if (!$messageId || $ackStr === null) {
                continue;
            }

            $estado = match (true) {
                in_array($ackStr, ['DELIVERY_ACK', 2]) => ['estado_entrega' => 'entregado', 'fecha_entregado' => now()],
                in_array($ackStr, ['READ', 'PLAYED', 3]) => ['estado_entrega' => 'leido', 'fecha_leido' => now()],
                in_array($ackStr, ['ERROR', 'FAILED', -1]) => ['estado_entrega' => 'fallido'],
                default => null,
            };

            if ($estado) {
                WaMensaje::where('evolution_message_id', $messageId)->update($estado);
            }
        }
    }

    // Mensajes entrantes (fromMe=false) → contador diario que consume el portal.
    // Increment atómico e idempotente por (taller, fecha).
    private function contarRecibidos(array $payload): void
    {
        if (! app()->bound('taller.actual')) {
            return;
        }

        $mensajes = data_get($payload, 'data', []);
        if (! is_array($mensajes) || empty($mensajes)) {
            return;
        }

        // El payload puede traer un solo mensaje (objeto) o una lista
        if (isset($mensajes['key'])) {
            $mensajes = [$mensajes];
        }

        $recibidos = collect($mensajes)
            ->filter(fn ($m) => data_get($m, 'key.fromMe') === false)
            ->count();

        if ($recibidos === 0) {
            return;
        }

        DB::statement(
            'INSERT INTO portal_wa_contadores_diarios (taller_id, fecha, recibidos, created_at, updated_at)
             VALUES (?, ?, ?, NOW(), NOW())
             ON DUPLICATE KEY UPDATE recibidos = recibidos + VALUES(recibidos), updated_at = NOW()',
            [app('taller.actual')->id, today()->toDateString(), $recibidos]
        );
    }

    // Cambios de estado de la instancia → historial de eventos del portal
    // + estado_conexion en wa_config.
    private function registrarCambioConexion(array $payload): void
    {
        if (! app()->bound('taller.actual')) {
            return;
        }

        $state = data_get($payload, 'data.state') ?? data_get($payload, 'state');

        $nuevo = match ($state) {
            'open'  => 'conectado',
            'close' => 'desconectado',
            default => null, // 'connecting' y otros intermedios no se registran
        };

        if ($nuevo === null) {
            return;
        }

        $tallerId = app('taller.actual')->id;
        $config = WaConfig::instancia();

        if ($config?->estado_conexion === $nuevo) {
            return; // sin cambio real
        }

        DB::table('portal_wa_eventos')->insert([
            'taller_id'       => $tallerId,
            'evento'          => $nuevo === 'conectado' ? 'conectado' : 'desconectado',
            'estado_anterior' => $config?->estado_conexion,
            'estado_nuevo'    => $nuevo,
            'origen'          => 'webhook',
            'detalle_json'    => json_encode(['state' => $state]),
            'created_at'      => now(),
        ]);

        $config?->update(['estado_conexion' => $nuevo]);
    }
}
