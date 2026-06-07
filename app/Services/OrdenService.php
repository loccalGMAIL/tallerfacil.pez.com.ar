<?php

namespace App\Services;

use App\Models\Orden;
use App\Models\OrdenEstadoHistorial;
use App\Models\Usuario;
use App\Models\WaRecordatorioConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class OrdenService
{
    public function __construct(private WhatsAppService $whatsApp) {}

    public function generarNumero(): string
    {
        $anio = now()->year;
        $ultimo = Orden::whereYear('created_at', $anio)
            ->lockForUpdate()
            ->count();

        return sprintf('OT-%d-%05d', $anio, $ultimo + 1);
    }

    public function crear(array $datos, Usuario $usuario): Orden
    {
        return DB::transaction(function () use ($datos, $usuario) {
            $orden = Orden::create([
                'numero'        => $this->generarNumero(),
                'vehiculo_id'   => $datos['vehiculo_id'],
                'mecanico_id'   => $datos['mecanico_id'] ?? null,
                'fecha_ingreso' => $datos['fecha_ingreso'] ?? now()->toDateString(),
                'km_ingreso'    => $datos['km_ingreso'] ?? null,
                'descripcion'   => $datos['descripcion'] ?? null,
                'estado'        => 'recepcion',
                'total_estimado' => 0,
            ]);

            $this->registrarCambioEstado($orden, 'recepcion', $usuario);

            return $orden;
        });
    }

    public function cambiarEstado(Orden $orden, string $nuevoEstado, Usuario $usuario, ?string $notas = null, bool $actualizarService = false): Orden
    {
        if (!$orden->puedeTransicionarA($nuevoEstado)) {
            throw new InvalidArgumentException(
                "No se puede pasar de '{$orden->estado}' a '{$nuevoEstado}'."
            );
        }

        $orden = DB::transaction(function () use ($orden, $nuevoEstado, $usuario, $notas, $actualizarService) {
            $orden->estado = $nuevoEstado;
            $orden->save();

            $this->registrarCambioEstado($orden, $nuevoEstado, $usuario, $notas);

            if ($nuevoEstado === 'entregado' && $actualizarService) {
                $vehiculo = $orden->vehiculo;
                $vehiculo->fecha_ultimo_service = $orden->fecha_ingreso;
                if ($orden->km_ingreso) {
                    $vehiculo->km_ultimo_service = $orden->km_ingreso;
                    $vehiculo->km_actual         = $orden->km_ingreso;
                }
                $vehiculo->save();
            }

            return $orden->fresh();
        });

        $this->autoEnviar($orden, $nuevoEstado);

        return $orden;
    }

    /**
     * Mueve la orden a cualquier columna del tablero (movimiento libre por drag-and-drop),
     * sin la restricción de la máquina de estados. Registra el cambio en el historial.
     */
    public function moverATablero(Orden $orden, string $nuevoEstado, Usuario $usuario): Orden
    {
        if (!in_array($nuevoEstado, Orden::ESTADOS_TABLERO)) {
            throw new InvalidArgumentException("'{$nuevoEstado}' no es una columna del tablero.");
        }

        if ($orden->estado === $nuevoEstado) {
            return $orden;
        }

        $orden = DB::transaction(function () use ($orden, $nuevoEstado, $usuario) {
            $orden->estado = $nuevoEstado;
            $orden->save();

            $this->registrarCambioEstado($orden, $nuevoEstado, $usuario, 'Movido desde el tablero');

            return $orden->fresh();
        });

        $this->autoEnviar($orden, $nuevoEstado);

        return $orden;
    }

    /**
     * Si está activado el envío automático para este estado, encola el mensaje de WhatsApp.
     * Nunca rompe el cambio de estado: cualquier error se loguea y se ignora.
     */
    private function autoEnviar(Orden $orden, string $estado): void
    {
        $flag = WaRecordatorioConfig::AUTO_ESTADOS[$estado] ?? null;
        if (!$flag) {
            return;
        }

        $config = WaRecordatorioConfig::instancia();
        if (!$config || !$config->{$flag}) {
            return;
        }

        try {
            // El tipo de plantilla coincide con el nombre del estado (recepcion/reparacion/listo/entregado)
            $this->whatsApp->enviarEvento($orden, $estado);
        } catch (Throwable $e) {
            Log::warning("Auto-envío WhatsApp falló para orden {$orden->id} ({$estado}): " . $e->getMessage());
        }
    }

    private function registrarCambioEstado(Orden $orden, string $estado, Usuario $usuario, ?string $notas = null): void
    {
        OrdenEstadoHistorial::create([
            'orden_id'   => $orden->id,
            'estado'     => $estado,
            'usuario_id' => $usuario->id,
            'notas'      => $notas,
            'fecha_hora' => now(),
        ]);
    }
}
