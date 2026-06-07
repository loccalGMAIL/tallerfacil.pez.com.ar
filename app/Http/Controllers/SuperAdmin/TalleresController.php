<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Suscripcion;
use App\Models\Taller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TalleresController extends Controller
{
    public function index(Request $request): View
    {
        $talleres = Taller::withCount('usuarios')
            ->with('suscripcionActual')
            ->when($request->search, fn ($q) => $q->where('nombre', 'like', "%{$request->search}%")
                ->orWhere('subdominio', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20);

        return view('superadmin.talleres.index', compact('talleres'));
    }

    public function create(): View
    {
        return view('superadmin.talleres.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre'         => ['required', 'string', 'max:150'],
            'subdominio'     => ['required', 'string', 'max:50', 'alpha_dash', 'unique:talleres,subdominio'],
            'email'          => ['nullable', 'email', 'max:150'],
            'telefono'       => ['nullable', 'string', 'max:30'],
            'admin_nombre'   => ['required', 'string', 'max:100'],
            'admin_email'    => ['required', 'email', 'max:150'],
            'admin_password' => ['required', 'string', 'min:8'],
        ]);

        $taller = Taller::create([
            'nombre'     => $datos['nombre'],
            'subdominio' => $datos['subdominio'],
            'email'      => $datos['email'] ?? null,
            'telefono'   => $datos['telefono'] ?? null,
            'activo'     => true,
            'en_prueba'  => true,
        ]);

        // Crear suscripción en período de prueba
        Suscripcion::create([
            'taller_id'  => $taller->id,
            'plan'       => 'basico',
            'estado'     => 'prueba',
        ]);

        // Crear usuario administrador inicial del taller
        Usuario::withoutGlobalScopes()->create([
            'nombre'    => $datos['admin_nombre'],
            'email'     => $datos['admin_email'],
            'password'  => Hash::make($datos['admin_password']),
            'rol'       => 'administrador',
            'activo'    => true,
            'taller_id' => $taller->id,
        ]);

        return redirect()->route('superadmin.talleres.show', $taller)
            ->with('success', "Taller '{$taller->nombre}' creado exitosamente.");
    }

    public function show(Taller $taller): View
    {
        $taller->load(['suscripciones.pagos', 'usuarios' => fn ($q) => $q->withoutGlobalScopes()]);

        return view('superadmin.talleres.show', compact('taller'));
    }

    public function edit(Taller $taller): View
    {
        return view('superadmin.talleres.edit', compact('taller'));
    }

    public function update(Request $request, Taller $taller): RedirectResponse
    {
        $datos = $request->validate([
            'nombre'     => ['required', 'string', 'max:150'],
            'subdominio' => ['required', 'string', 'max:50', 'alpha_dash', "unique:talleres,subdominio,{$taller->id}"],
            'email'      => ['nullable', 'email', 'max:150'],
            'telefono'   => ['nullable', 'string', 'max:30'],
            'direccion'  => ['nullable', 'string', 'max:255'],
            'razon_social' => ['nullable', 'string', 'max:150'],
            'cuit'       => ['nullable', 'string', 'max:20'],
        ]);

        $taller->update($datos);

        return back()->with('success', 'Taller actualizado.');
    }

    public function activar(Taller $taller): RedirectResponse
    {
        $taller->update(['activo' => true]);
        return back()->with('success', "Taller '{$taller->nombre}' activado.");
    }

    public function desactivar(Taller $taller): RedirectResponse
    {
        $taller->update(['activo' => false]);
        return back()->with('success', "Taller '{$taller->nombre}' desactivado.");
    }
}
