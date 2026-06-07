<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\OrdenTarea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrdenTareaController extends Controller
{
    public function store(Request $request, Orden $orden): RedirectResponse
    {
        if (!$orden->estaAbierta()) {
            return back()->with('error', 'No se pueden agregar tareas a una orden cerrada.');
        }

        $request->validate(['descripcion' => ['required', 'string', 'max:255']]);

        $orden->tareas()->create([
            'descripcion' => $request->descripcion,
            'posicion'    => (int) $orden->tareas()->max('posicion') + 1,
            'completada'  => false,
        ]);

        return back()->with('success', 'Tarea agregada.');
    }

    public function toggle(Orden $orden, OrdenTarea $tarea): RedirectResponse
    {
        abort_unless($tarea->orden_id === $orden->id, 404);

        $tarea->update(['completada' => !$tarea->completada]);

        return back();
    }

    public function destroy(Orden $orden, OrdenTarea $tarea): RedirectResponse
    {
        abort_unless($tarea->orden_id === $orden->id, 404);

        $tarea->delete();

        return back()->with('success', 'Tarea eliminada.');
    }
}
