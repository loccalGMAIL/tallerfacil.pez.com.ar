<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NegocioController extends Controller
{
    public function edit(): View
    {
        return view('configuracion.negocio', ['negocio' => app('taller.actual')]);
    }

    public function update(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'razon_social' => ['nullable', 'string', 'max:150'],
            'cuit'         => ['nullable', 'string', 'max:20'],
            'telefono'     => ['nullable', 'string', 'max:30'],
            'email'        => ['nullable', 'email', 'max:150'],
            'direccion'    => ['nullable', 'string', 'max:255'],
            'horario'      => ['nullable', 'string', 'max:255'],
            'notas'        => ['nullable', 'string'],
        ], ['nombre.required' => 'El nombre del taller es obligatorio.']);

        app('taller.actual')->update($datos);

        return back()->with('success', 'Datos del negocio guardados.');
    }
}

