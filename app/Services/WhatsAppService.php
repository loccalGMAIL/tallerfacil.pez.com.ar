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
    /** Tipos de evento que tienen plantilla y se disparan desde una orden. */
    public const TIPOS_EVENTO = ['presupuesto', 'recepcion', 'reparacion', 'listo', 'entregado'];

    public function enviarPresupuesto(Orden $orden): WaMensaje
    {
        return $this->enviarEvento($orden, 'presupuesto');
    }

    public function enviarRecepcion(Orden $orden): WaMensaje
    {
        return $this->enviarEvento($orden, 'recepcion');
    }

    /**
     * Envía un mensaje de evento (plantilla atada a un punto del flujo) para una orden.
     */
    public function enviarEvento(Orden $orden, string $tipo): WaMensaje
    {
        $orden->load(['vehiculo.cliente', 'items']);
        $cliente  = $orden->vehiculo->cliente;
        $vehiculo = $orden->vehiculo;

        $plantilla = WaPlantilla::activa($tipo);
        if (!$plantilla) {
            throw new Exception("No hay plantilla activa de «{$tipo}».");
        }

        $contenido = $this->renderizar($plantilla->texto, $this->variables($orden));

        return $this->crearYEncolar($cliente, $contenido, $tipo, [
            'orden_id'    => $orden->id,
            'vehiculo_id' => $vehiculo->id,
        ]);
    }

    /**
     * Envía un mensaje manual/libre (con variables) para una orden.
     */
    public function enviarManual(Orden $orden, string $texto): WaMensaje
    {
        $orden->load(['vehiculo.cliente', 'items']);
        $cliente  = $orden->vehiculo->cliente;
        $vehiculo = $orden->vehiculo;

        $contenido = $this->renderizar($texto, $this->variables($orden));

        return $this->crearYEncolar($cliente, $contenido, 'manual', [
            'orden_id'    => $orden->id,
            'vehiculo_id' => $vehiculo->id,
        ]);
    }

    /**
     * Set de variables disponibles para renderizar plantillas de una orden.
     */
    private function variables(Orden $orden): array
    {
        $cliente  = $orden->vehiculo->cliente;
        $vehiculo = $orden->vehiculo;

        $itemsLista = $orden->items->map(fn ($item) =>
            "• {$item->descripcion} x{$item->cantidad}: \$" . number_format($item->subtotal, 2, ',', '.')
        )->join("\n");

        return [
            'nombre'        => $cliente->nombre,
            'marca'         => $vehiculo->marca,
            'modelo'        => $vehiculo->modelo,
            'patente'       => $vehiculo->patente,
            'total'         => number_format($orden->total_estimado, 2, ',', '.'),
            'numero_orden'  => $orden->numero,
            'fecha_ingreso' => $orden->fecha_ingreso->format('d/m/Y'),
            'items_lista'   => $itemsLista ?: '(sin ítems)',
        ];
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
