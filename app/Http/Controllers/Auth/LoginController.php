<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $esPanelAdmin = str_starts_with($request->getHost(), 'admin.');

        if ($esPanelAdmin) {
            // Solo superadmin puede ingresar al panel de administración
            if (Auth::attempt([
                'email'    => $credentials['email'],
                'password' => $credentials['password'],
                'rol'      => 'superadmin',
                'activo'   => true,
            ], $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->route('superadmin.dashboard');
            }
        } elseif (app()->bound('taller.actual')) {
            // Login del taller: valida que el usuario pertenezca a este taller
            $taller = app('taller.actual');
            if (Auth::attempt([
                'email'     => $credentials['email'],
                'password'  => $credentials['password'],
                'taller_id' => $taller->id,
                'activo'    => true,
            ], $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('ordenes.index'));
            }
        } else {
            // Fallback para entornos de desarrollo sin subdominio
            if (Auth::attempt([
                'email'    => $credentials['email'],
                'password' => $credentials['password'],
                'activo'   => true,
            ], $request->boolean('remember'))) {
                $request->session()->regenerate();
                $usuario = Auth::user();
                if ($usuario->esSuperAdmin()) {
                    return redirect()->route('superadmin.dashboard');
                }
                return redirect()->intended(route('ordenes.index'));
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
