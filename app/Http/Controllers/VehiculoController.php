<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehiculoRequest;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehiculoController extends Controller
{
    public function index(Request $request): View
    {
        $vehiculos = Vehiculo::with('cliente')
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->patente, fn ($q) => $q->where('patente', 'like', '%' . strtoupper($request->patente) . '%'))
            ->when($request->marca, fn ($q) => $q->where('marca', 'like', '%' . $request->marca . '%'))
            ->activos()
            ->orderBy('patente')
            ->paginate(20)
            ->withQueryString();

        return view('vehiculos.index', compact('vehiculos'));
    }

    public function create(Request $request): View
    {
        $cliente = $request->cliente_id ? Cliente::findOrFail($request->cliente_id) : null;
        $clientes = Cliente::activos()->orderBy('nombre')->get(['id', 'nombre', 'telefono_display']);

        return view('vehiculos.create', compact('cliente', 'clientes'));
    }

    public function store(StoreVehiculoRequest $request): RedirectResponse
    {
        $vehiculo = Vehiculo::create($request->validated());

        return redirect()->route('vehiculos.show', $vehiculo)
            ->with('success', "Vehículo {$vehiculo->patente} registrado correctamente.");
    }

    public function show(Vehiculo $vehiculo): View
    {
        $vehiculo->load('cliente');
        $ordenes = $vehiculo->ordenes()
            ->with('mecanico')
            ->orderBy('fecha_ingreso', 'desc')
            ->paginate(10);

        return view('vehiculos.show', compact('vehiculo', 'ordenes'));
    }

    public function edit(Vehiculo $vehiculo): View
    {
        $clientes = Cliente::activos()->orderBy('nombre')->get(['id', 'nombre', 'telefono_display']);

        return view('vehiculos.edit', compact('vehiculo', 'clientes'));
    }

    public function update(StoreVehiculoRequest $request, Vehiculo $vehiculo): RedirectResponse
    {
        $vehiculo->update($request->validated());

        return redirect()->route('vehiculos.show', $vehiculo)
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehiculo $vehiculo): RedirectResponse
    {
        if ($vehiculo->ordenes()->whereNotIn('estado', ['entregada', 'cancelada'])->exists()) {
            return back()->with('error', 'No se puede desactivar un vehículo con órdenes abiertas.');
        }

        $vehiculo->update(['activo' => false]);

        return back()->with('success', 'Vehículo desactivado.');
    }
}
