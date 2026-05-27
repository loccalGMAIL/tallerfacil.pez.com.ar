<?php

namespace App\Services;

use App\Models\Orden;
use App\Models\OrdenEstadoHistorial;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrdenService
{
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
                'estado'        => 'presupuesto',
                'total_estimado' => 0,
            ]);

            $this->registrarCambioEstado($orden, 'presupuesto', $usuario);

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

        return DB::transaction(function () use ($orden, $nuevoEstado, $usuario, $notas, $actualizarService) {
            $orden->estado = $nuevoEstado;
            $orden->save();

            $this->registrarCambioEstado($orden, $nuevoEstado, $usuario, $notas);

            if ($nuevoEstado === 'entregada' && $actualizarService) {
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
