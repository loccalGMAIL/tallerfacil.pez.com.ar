<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Models\WaMensaje;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecordatorioController extends Controller
{
    public function paraRecordatorio(): JsonResponse
    {
        $vehiculos = DB::select('SELECT * FROM v_vehiculos_para_recordatorio');

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

        $vehiculo = Vehiculo::find($datos['vehiculo_id']);

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
