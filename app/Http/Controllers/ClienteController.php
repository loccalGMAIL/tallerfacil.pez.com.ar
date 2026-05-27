<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $clientes = Cliente::query()
            ->when($request->search, fn ($q) => $q->buscar($request->search))
            ->when($request->has('activo'), fn ($q) => $q->where('activo', $request->boolean('activo')))
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function create(): View
    {
        return view('clientes.create');
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['telefono_normalizado'] = $request->telefono_normalizado;

        $cliente = Cliente::create($datos);

        return redirect()->route('clientes.show', $cliente)
            ->with('success', "Cliente {$cliente->nombre} creado correctamente.");
    }

    public function show(Cliente $cliente): View
    {
        $vehiculos = $cliente->vehiculos()->with('ordenes')->orderBy('patente')->get();
        $mensajes  = $cliente->waMensajes()->latest('fecha_hora')->limit(10)->get();

        return view('clientes.show', compact('cliente', 'vehiculos', 'mensajes'));
    }

    public function edit(Cliente $cliente): View
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(StoreClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $datos = $request->validated();
        $datos['telefono_normalizado'] = $request->telefono_normalizado;

        $cliente->update($datos);

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        if ($cliente->tieneOrdenesAbiertas()) {
            return back()->with('error', 'No se puede desactivar un cliente con órdenes abiertas.');
        }

        $cliente->update(['activo' => false]);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente desactivado correctamente.');
    }
}
