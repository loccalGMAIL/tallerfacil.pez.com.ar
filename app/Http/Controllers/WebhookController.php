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
        $updates = data_get($payload, 'data', [data_get($payload, 'data', $payload)]);
        if (!is_array($updates)) {
            $updates = [$updates];
        }

        foreach ($updates as $update) {
            $messageId = data_get($update, 'key.id') ?? data_get($update, 'id');
            $ack       = data_get($update, 'update.message.ackStatus')
                      ?? data_get($update, 'ack')
                      ?? data_get($update, 'status');

            if (!$messageId || $ack === null) {
                continue;
            }

            $estado = match ((int) $ack) {
                2 => ['estado_entrega' => 'entregado', 'fecha_entregado' => now()],
                3 => ['estado_entrega' => 'leido',     'fecha_leido'     => now()],
                -1 => ['estado_entrega' => 'fallido'],
                default => null,
            };

            if ($estado) {
                WaMensaje::where('evolution_message_id', $messageId)->update($estado);
            }
        }
    }
}
