<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitacion;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        $params = [];

        if ($request->filled('invitation')) {
            $params['state'] = $request->get('invitation');
        }

        $driver = Socialite::driver('google');

        if (! empty($params)) {
            $driver = $driver->with($params);
        }

        return $driver->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()->route('login')->withErrors(['email' => 'No se pudo autenticar con Google. Intentá de nuevo.']);
        }

        // CASO E — usuario ya autenticado: vincular cuenta de Google
        if (Auth::check()) {
            $existente = Usuario::where('google_id', $googleUser->getId())->first();

            if ($existente && $existente->id !== Auth::id()) {
                return redirect()->route('perfil.edit')->withErrors(['google' => 'Esta cuenta de Google ya está vinculada a otro usuario.']);
            }

            Auth::user()->update([
                'google_id'     => $googleUser->getId(),
                'google_avatar' => $googleUser->getAvatar(),
            ]);

            return redirect()->route('perfil.edit')->with('success', 'Cuenta de Google vinculada correctamente.');
        }

        // CASO A — google_id ya registrado
        $usuario = Usuario::where('google_id', $googleUser->getId())->first();

        if ($usuario) {
            if (! $usuario->activo) {
                return redirect()->route('login')->withErrors(['email' => 'Tu cuenta está desactivada.']);
            }

            Auth::login($usuario);
            $request->session()->regenerate();

            return redirect()->intended(route('ordenes.index'));
        }

        // CASO B — email existente sin google_id
        $usuario = Usuario::where('email', $googleUser->getEmail())->first();

        if ($usuario) {
            if (! $usuario->activo) {
                return redirect()->route('login')->withErrors(['email' => 'Tu cuenta está desactivada.']);
            }

            $usuario->update([
                'google_id'     => $googleUser->getId(),
                'google_avatar' => $googleUser->getAvatar(),
            ]);

            Auth::login($usuario);
            $request->session()->regenerate();

            return redirect()->intended(route('ordenes.index'));
        }

        // CASO C — no existe: verificar token de invitación en state
        $token = $request->get('state');

        if ($token) {
            $invitacion = Invitacion::where('token', $token)->first();

            if ($invitacion && $invitacion->estaVigente()) {
                $usuario = Usuario::create([
                    'nombre'        => $invitacion->nombre,
                    'email'         => $invitacion->email,
                    'rol'           => $invitacion->rol,
                    'activo'        => true,
                    'password'      => null,
                    'google_id'     => $googleUser->getId(),
                    'google_avatar' => $googleUser->getAvatar(),
                ]);

                $invitacion->update(['used_at' => now()]);

                Auth::login($usuario);
                $request->session()->regenerate();

                return redirect()->route('ordenes.index')->with('success', "¡Bienvenido/a, {$usuario->nombre}!");
            }
        }

        // CASO D — sin invitación válida
        return redirect()->route('login')->withErrors(['email' => 'No tenés acceso. Pedí una invitación al administrador.']);
    }
}
