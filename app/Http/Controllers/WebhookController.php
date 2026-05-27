<?php

namespace App\Http\Controllers;

use App\Models\WaMensaje;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
}
