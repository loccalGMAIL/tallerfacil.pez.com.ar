<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Models\WaMensaje;
use App\Models\WaRecordatorioConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecordatorioController extends Controller
{
    public function paraRecordatorio(): JsonResponse
    {
        $config = WaRecordatorioConfig::instancia();

        if (!$config || !$config->activo) {
            return response()->json([]);
        }

        $umbralMeses        = $config->umbral_meses ?? 6;
        $ventanaMinimaDias  = $config->ventana_minima_dias ?? 30;

        // IDs de vehículos que ya recibieron recordatorio recientemente
        $yaNotificados = WaMensaje::where('tipo', 'recordatorio')
            ->whereIn('estado_entrega', ['enviado', 'entregado', 'leido'])
            ->where('fecha_hora', '>=', now()->subDays($ventanaMinimaDias))
            ->pluck('vehiculo_id');

        $vehiculos = Vehiculo::with('cliente')
            ->activos()
            ->whereNotNull('fecha_ultimo_service')
            ->whereNotIn('id', $yaNotificados)
            ->whereHas('cliente', fn ($q) => $q->activos())
            ->get()
            ->filter(function ($v) use ($umbralMeses) {
                // Calcular meses desde el último service en PHP (compatible con SQLite y MySQL)
                if (!$v->fecha_ultimo_service) {
                    return false;
                }
                $meses = $v->fecha_ultimo_service->diffInMonths(now());
                return $meses >= $umbralMeses;
            })
            ->map(fn ($v) => [
                'vehiculo_id'          => $v->id,
                'patente'              => $v->patente,
                'marca'                => $v->marca,
                'modelo'               => $v->modelo,
                'anio'                 => $v->anio,
                'km_actual'            => $v->km_actual,
                'km_ultimo_service'    => $v->km_ultimo_service,
                'fecha_ultimo_service' => $v->fecha_ultimo_service?->toDateString(),
                'cliente_id'           => $v->cliente->id,
                'nombre'               => $v->cliente->nombre,
                'telefono'             => $v->cliente->telefono_normalizado,
                'email'                => $v->cliente->email,
                'meses_desde_service'  => (int) $v->fecha_ultimo_service->diffInMonths(now()),
                'km_desde_service'     => $v->km_actual && $v->km_ultimo_service
                    ? ($v->km_actual - $v->km_ultimo_service)
                    : null,
                'tipo_recordatorio'    => 'service_fecha',
            ])
            ->values();

        return response()->json($vehiculos);
    }

    public function log(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'vehiculo_id'          => ['required', 'exists:vehiculos,id'],
            'cliente_id'           => ['required', 'exists:clientes,id'],
            'tipo'                 => ['required', 'in:recordatorio'],
            'contenido'            => ['required', 'string'],
            'estado_entrega'       => ['required', 'in:enviado,fallido'],
            'evolution_message_id' => ['nullable', 'string'],
            'error_detalle'        => ['nullable', 'string'],
        ]);

        $mensaje = WaMensaje::create([
            'cliente_id'           => $datos['cliente_id'],
            'vehiculo_id'          => $datos['vehiculo_id'],
            'tipo'                 => 'recordatorio',
            'contenido'            => $datos['contenido'],
            'origen'               => 'n8n',
            'estado_entrega'       => $datos['estado_entrega'],
            'evolution_message_id' => $datos['evolution_message_id'] ?? null,
            'error_detalle'        => $datos['error_detalle'] ?? null,
            'fecha_hora'           => now(),
        ]);

        return response()->json(['id' => $mensaje->id], 201);
    }
}
