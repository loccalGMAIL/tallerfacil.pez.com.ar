<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Taller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuariosController extends Controller
{
    public function index(Request $request): View
    {
        $usuarios = Usuario::withoutGlobalScopes()
            ->with('taller')
            ->when($request->search, fn ($q) => $q->where('nombre', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->taller_id, fn ($q) => $q->where('taller_id', $request->taller_id))
            ->when($request->rol, fn ($q) => $q->where('rol', $request->rol))
            ->orderBy('nombre')
            ->paginate(25);

        $talleres = Taller::orderBy('nombre')->get(['id', 'nombre']);

        return view('superadmin.usuarios.index', compact('usuarios', 'talleres'));
    }

    public function edit(Usuario $usuario): View
    {
        $talleres = Taller::orderBy('nombre')->get(['id', 'nombre']);
        return view('superadmin.usuarios.edit', compact('usuario', 'talleres'));
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $datos = $request->validate([
            'nombre'    => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:150'],
            'rol'       => ['required', 'in:administrador,mecanico'],
            'activo'    => ['boolean'],
        ]);

        $usuario->withoutGlobalScopes()->find($usuario->id)->update($datos);

        return back()->with('success', 'Usuario actualizado.');
    }

    public function resetPassword(Usuario $usuario): RedirectResponse
    {
        $taller = $usuario->taller_id ? Taller::find($usuario->taller_id) : null;

        $token = app('auth.password.broker')->createToken($usuario);

        $subdominio = $taller?->subdominio ?? 'app';
        $baseDomain = config('app.base_domain', 'tallerfacil.com.ar');
        $link       = "https://{$subdominio}.{$baseDomain}/reset-password/{$token}?email=" . urlencode($usuario->email);

        // Si hay configuración de mail, enviar. Si no, mostrar el link en sesión.
        return back()->with('reset_link', $link)
            ->with('success', "Link de reset generado para {$usuario->email}.");
    }
}
