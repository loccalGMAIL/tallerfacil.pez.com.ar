<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\OrdenFoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrdenFotoController extends Controller
{
    public function store(Request $request, Orden $orden): RedirectResponse
    {
        $request->validate([
            'foto'        => ['required', 'image', 'max:5120'],
            'tipo'        => ['nullable', 'in:patente,recepcion,trabajo'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ], [
            'foto.required' => 'Seleccioná o sacá una foto.',
            'foto.image'    => 'El archivo debe ser una imagen.',
            'foto.max'      => 'La imagen no puede superar los 5 MB.',
        ]);

        $ruta = $request->file('foto')->store("ordenes/{$orden->id}", 'public');

        $orden->fotos()->create([
            'ruta'        => $ruta,
            'tipo'        => $request->input('tipo', 'recepcion'),
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Foto agregada.');
    }

    public function destroy(Orden $orden, OrdenFoto $foto): RedirectResponse
    {
        abort_unless($foto->orden_id === $orden->id, 404);

        Storage::disk('public')->delete($foto->ruta);
        $foto->delete();

        return back()->with('success', 'Foto eliminada.');
    }
}
