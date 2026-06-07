<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Orden;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $esMecanico = auth()->user()->rol === 'mecanico';
        $mecanicoId = auth()->id();

        // Órdenes abiertas agrupadas por columna del tablero
        $ordenes = Orden::with(['vehiculo.cliente'])
            ->whereIn('estado', Orden::ESTADOS_TABLERO)
            ->when($esMecanico, fn ($q) => $q->where('mecanico_id', $mecanicoId))
            ->orderBy('fecha_ingreso', 'desc')
            ->get();

        $columnas = [];
        foreach (Orden::ESTADOS_TABLERO as $estado) {
            $columnas[$estado] = $ordenes->where('estado', $estado)->values();
        }

        // KPIs del mes en curso (basados en el historial de estados)
        $inicioMes = now()->startOfMonth();
        $finMes    = now()->endOfMonth();

        $idsEntregadosMes = $this->ordenesConEstadoEnRango('entregado', $inicioMes, $finMes, $esMecanico, $mecanicoId);
        $idsCotizadosMes  = $this->ordenesConEstadoEnRango('cotizacion', $inicioMes, $finMes, $esMecanico, $mecanicoId);

        $kpis = [
            'ingresos'             => Orden::whereIn('id', $idsEntregadosMes)->sum('total_estimado'),
            'total_cotizado'       => Orden::whereIn('id', $idsCotizadosMes)->sum('total_estimado'),
            'vehiculos_entregados' => count($idsEntregadosMes),
        ];

        // Datos para el modal de ingreso
        $clientes = Cliente::activos()->orderBy('nombre')->get(['id', 'nombre', 'telefono_display']);

        // Vista calendario
        $vista = $request->input('vista', 'tablero') === 'calendario' ? 'calendario' : 'tablero';
        $calendario = $vista === 'calendario'
            ? $this->datosCalendario($request->input('mes'), $esMecanico, $mecanicoId)
            : null;

        return view('dashboard.index', compact('columnas', 'kpis', 'clientes', 'vista', 'calendario'));
    }

    private function ordenesConEstadoEnRango(string $estado, $desde, $hasta, bool $esMecanico, int $mecanicoId): array
    {
        return Orden::whereHas('historial', function ($q) use ($estado, $desde, $hasta) {
                $q->where('estado', $estado)->whereBetween('fecha_hora', [$desde, $hasta]);
            })
            ->when($esMecanico, fn ($q) => $q->where('mecanico_id', $mecanicoId))
            ->pluck('id')
            ->all();
    }

    /**
     * Arma la grilla mensual con las órdenes agrupadas por día de ingreso.
     */
    private function datosCalendario(?string $mesParam, bool $esMecanico, int $mecanicoId): array
    {
        $ref = $mesParam
            ? \Carbon\Carbon::createFromFormat('Y-m', $mesParam)->startOfMonth()
            : now()->startOfMonth();

        $inicio = $ref->copy()->startOfMonth();
        $fin    = $ref->copy()->endOfMonth();

        $ordenes = Orden::with('vehiculo')
            ->whereBetween('fecha_ingreso', [$inicio, $fin])
            ->when($esMecanico, fn ($q) => $q->where('mecanico_id', $mecanicoId))
            ->orderBy('fecha_ingreso')
            ->get()
            ->groupBy(fn ($o) => $o->fecha_ingreso->format('Y-m-d'));

        // Construir celdas: arrancamos el lunes previo al día 1
        $primerDia = $inicio->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $ultimoDia = $fin->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $dias = [];
        for ($d = $primerDia->copy(); $d->lte($ultimoDia); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $dias[] = [
                'fecha'      => $d->copy(),
                'delMes'     => $d->month === $ref->month,
                'esHoy'      => $d->isToday(),
                'ordenes'    => $ordenes->get($key, collect()),
            ];
        }

        return [
            'ref'       => $ref,
            'mes_label' => ucfirst($ref->locale('es')->isoFormat('MMMM YYYY')),
            'prev'      => $ref->copy()->subMonth()->format('Y-m'),
            'next'      => $ref->copy()->addMonth()->format('Y-m'),
            'dias'      => $dias,
        ];
    }
}
