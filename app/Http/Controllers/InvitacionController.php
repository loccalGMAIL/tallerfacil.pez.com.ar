<?php

namespace App\Http\Controllers;

use App\Models\Invitacion;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InvitacionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email'  => 'required|email|max:150|unique:usuarios,email',
            'rol'    => 'required|in:administrador,mecanico',
        ]);

        $token = Str::random(64);

        Invitacion::create([
            'token'      => $token,
            'email'      => $request->email,
            'nombre'     => $request->nombre,
            'rol'        => $request->rol,
            'expires_at' => now()->addDays(7),
            'created_by' => Auth::id(),
        ]);

        $link = route('invitacion.show', $token);

        return back()->with('invitacion_link', $link)->with('invitacion_nombre', $request->nombre);
    }

    public function show(string $token): View|RedirectResponse
    {
        $invitacion = Invitacion::where('token', $token)->firstOrFail();

        if (! is_null($invitacion->used_at)) {
            return view('auth.invitacion', ['invitacion' => $invitacion, 'estado' => 'usada']);
        }

        if ($invitacion->expires_at->isPast()) {
            return view('auth.invitacion', ['invitacion' => $invitacion, 'estado' => 'expirada']);
        }

        return view('auth.invitacion', ['invitacion' => $invitacion, 'estado' => 'vigente']);
    }

    public function register(Request $request, string $token): RedirectResponse
    {
        $invitacion = Invitacion::where('token', $token)->firstOrFail();

        if (! $invitacion->estaVigente()) {
            return redirect()->route('invitacion.show', $token);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $usuario = Usuario::create([
            'nombre'   => $invitacion->nombre,
            'email'    => $invitacion->email,
            'rol'      => $invitacion->rol,
            'activo'   => true,
            'password' => Hash::make($request->password),
        ]);

        $invitacion->update(['used_at' => now()]);

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('ordenes.index')->with('success', "¡Bienvenido/a, {$usuario->nombre}!");
    }

    public function destroy(Invitacion $invitacion): RedirectResponse
    {
        if (! is_null($invitacion->used_at)) {
            return back()->withErrors(['invitacion' => 'No se puede revocar una invitación ya utilizada.']);
        }

        $invitacion->delete();

        return back()->with('success', 'Invitación revocada.');
    }
}
