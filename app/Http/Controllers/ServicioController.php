<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServicioController extends Controller
{
    public function index(): View
    {
        $servicios = Servicio::orderBy('nombre')->get();

        return view('configuracion.servicios', compact('servicios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->reglas());

        Servicio::create($request->only('nombre', 'descripcion', 'tipo', 'precio') + [
            'activo' => $request->boolean('activo', true),
        ]);

        return back()->with('success', 'Servicio agregado.');
    }

    public function update(Request $request, Servicio $servicio): RedirectResponse
    {
        $request->validate($this->reglas());

        $servicio->update($request->only('nombre', 'descripcion', 'tipo', 'precio') + [
            'activo' => $request->boolean('activo'),
        ]);

        return back()->with('success', 'Servicio actualizado.');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        $servicio->delete();

        return back()->with('success', 'Servicio eliminado.');
    }

    private function reglas(): array
    {
        return [
            'nombre'      => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'tipo'        => ['required', 'in:mano_obra,repuesto'],
            'precio'      => ['required', 'numeric', 'min:0'],
        ];
    }
}
