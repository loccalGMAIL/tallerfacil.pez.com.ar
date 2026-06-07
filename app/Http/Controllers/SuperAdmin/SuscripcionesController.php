<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Suscripcion;
use App\Models\Taller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuscripcionesController extends Controller
{
    public function index(Request $request): View
    {
        $suscripciones = Suscripcion::with('taller')
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado))
            ->when($request->taller_id, fn ($q) => $q->where('taller_id', $request->taller_id))
            ->latest()
            ->paginate(25);

        $talleres = Taller::orderBy('nombre')->get(['id', 'nombre']);

        return view('superadmin.suscripciones.index', compact('suscripciones', 'talleres'));
    }

    public function show(Taller $taller): View
    {
        $suscripciones = Suscripcion::where('taller_id', $taller->id)
            ->with('pagos')
            ->latest()
            ->get();

        return view('superadmin.suscripciones.show', compact('taller', 'suscripciones'));
    }

    public function activar(Request $request, Suscripcion $suscripcion): RedirectResponse
    {
        $datos = $request->validate([
            'plan'               => ['required', 'in:basico,estandar,premium'],
            'fecha_vencimiento'  => ['nullable', 'date', 'after:today'],
        ]);

        $suscripcion->update([
            'estado'            => 'activo',
            'plan'              => $datos['plan'],
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
        ]);

        return back()->with('success', 'Suscripción activada.');
    }

    public function cancelar(Suscripcion $suscripcion): RedirectResponse
    {
        $suscripcion->update(['estado' => 'cancelado']);
        return back()->with('success', 'Suscripción cancelada.');
    }
}
