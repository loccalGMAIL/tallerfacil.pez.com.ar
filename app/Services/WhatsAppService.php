<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Orden;
use App\Models\WaMensaje;
use App\Models\WaPlantilla;
use App\Jobs\EnviarMensajeWhatsAppJob;
use Exception;

class WhatsAppService
{
    public function enviarPresupuesto(Orden $orden): WaMensaje
    {
        $orden->load(['vehiculo.cliente', 'items']);
        $cliente  = $orden->vehiculo->cliente;
        $vehiculo = $orden->vehiculo;

        $plantilla = WaPlantilla::activa('presupuesto');
        if (!$plantilla) {
            throw new Exception('No hay plantilla activa de presupuesto.');
        }

        $itemsLista = $orden->items->map(fn ($item) =>
            "• {$item->descripcion} x{$item->cantidad}: \$" . number_format($item->subtotal, 2, ',', '.')
        )->join("\n");

        $contenido = $this->renderizar($plantilla->texto, [
            'nombre'      => $cliente->nombre,
            'marca'       => $vehiculo->marca,
            'modelo'      => $vehiculo->modelo,
            'patente'     => $vehiculo->patente,
            'total'       => number_format($orden->total_estimado, 2, ',', '.'),
            'numero_orden' => $orden->numero,
            'items_lista' => $itemsLista ?: '(sin ítems)',
        ]);

        return $this->crearYEncolar($cliente, $contenido, 'presupuesto', ['orden_id' => $orden->id, 'vehiculo_id' => $vehiculo->id]);
    }

    public function enviarRecepcion(Orden $orden): WaMensaje
    {
        $orden->load('vehiculo.cliente');
        $cliente  = $orden->vehiculo->cliente;
        $vehiculo = $orden->vehiculo;

        $plantilla = WaPlantilla::activa('recepcion');
        if (!$plantilla) {
            throw new Exception('No hay plantilla activa de recepción.');
        }

        $contenido = $this->renderizar($plantilla->texto, [
            'nombre'        => $cliente->nombre,
            'marca'         => $vehiculo->marca,
            'modelo'        => $vehiculo->modelo,
            'patente'       => $vehiculo->patente,
            'numero_orden'  => $orden->numero,
            'fecha_ingreso' => $orden->fecha_ingreso->format('d/m/Y'),
        ]);

        return $this->crearYEncolar($cliente, $contenido, 'recepcion', ['orden_id' => $orden->id, 'vehiculo_id' => $vehiculo->id]);
    }

    public function renderizar(string $texto, array $variables): string
    {
        foreach ($variables as $clave => $valor) {
            $texto = str_replace('{' . $clave . '}', $valor, $texto);
        }

        return $texto;
    }

    private function crearYEncolar(Cliente $cliente, string $contenido, string $tipo, array $extras = []): WaMensaje
    {
        $mensaje = WaMensaje::create(array_merge([
            'cliente_id'    => $cliente->id,
            'tipo'          => $tipo,
            'contenido'     => $contenido,
            'origen'        => 'php',
            'estado_entrega' => 'pendiente',
            'fecha_hora'    => now(),
        ], $extras));

        EnviarMensajeWhatsAppJob::dispatch($mensaje->id, $cliente->telefono_normalizado, $contenido);

        return $mensaje;
    }
}
