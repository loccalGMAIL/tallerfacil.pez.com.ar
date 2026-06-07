<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Services\ClienteService;
use App\Services\OrdenService;
use App\Services\VehiculoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecepcionController extends Controller
{
    public function __construct(
        private OrdenService $ordenService,
        private ClienteService $clienteService,
        private VehiculoService $vehiculoService,
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'modo'            => ['required', 'in:recepcion,cotizacion'],
            'cliente_id'      => ['nullable', 'exists:clientes,id'],
            'cliente_nombre'  => ['required_without:cliente_id', 'nullable', 'string', 'max:150'],
            'cliente_telefono' => ['required_without:cliente_id', 'nullable', 'string', 'max:30'],
            'patente'         => ['required', 'string', 'max:10'],
            'marca'           => ['nullable', 'string', 'max:50'],
            'modelo'          => ['nullable', 'string', 'max:100'],
            'anio'            => ['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1)],
            'km_ingreso'      => ['nullable', 'integer', 'min:0'],
        ], [
            'cliente_nombre.required_without'   => 'El nombre del cliente es obligatorio.',
            'cliente_telefono.required_without' => 'El teléfono del cliente es obligatorio.',
            'patente.required'                  => 'La patente es obligatoria.',
        ]);

        // Normalizar y validar patente
        try {
            $patente = $this->vehiculoService->normalizarPatente($request->patente);
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        try {
            $orden = DB::transaction(function () use ($request, $patente) {
                // 1) Cliente: existente o nuevo
                if ($request->filled('cliente_id')) {
                    $cliente = Cliente::findOrFail($request->cliente_id);
                } else {
                    $normalizado = $this->clienteService->normalizarTelefono($request->cliente_telefono);
                    $cliente = Cliente::firstOrCreate(
                        ['telefono_normalizado' => $normalizado],
                        [
                            'nombre'           => $request->cliente_nombre,
                            'tipo_doc'         => 'DNI',
                            'telefono_display' => $request->cliente_telefono,
                            'activo'           => true,
                        ]
                    );
                }

                // 2) Vehículo: por patente (existente) o nuevo bajo el cliente
                $vehiculo = Vehiculo::where('patente', $patente)->first();
                if (!$vehiculo) {
                    $vehiculo = Vehiculo::create([
                        'cliente_id' => $cliente->id,
                        'patente'    => $patente,
                        'marca'      => $request->marca ?: 'N/D',
                        'modelo'     => $request->modelo ?: 'N/D',
                        'anio'       => $request->anio,
                        'activo'     => true,
                    ]);
                }

                // 3) Orden (arranca en recepción)
                $orden = $this->ordenService->crear([
                    'vehiculo_id' => $vehiculo->id,
                    'km_ingreso'  => $request->km_ingreso,
                ], auth()->user());

                // 4) Cotización rápida → pasar a estado cotización
                if ($request->modo === 'cotizacion') {
                    $this->ordenService->cambiarEstado($orden, 'cotizacion', auth()->user());
                }

                return $orden;
            });
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        if ($request->modo === 'cotizacion') {
            return redirect()->route('ordenes.show', $orden)
                ->with('success', "Cotización iniciada — orden {$orden->numero}.");
        }

        return redirect()->route('dashboard')
            ->with('success', "Vehículo ingresado — orden {$orden->numero}.");
    }
}
