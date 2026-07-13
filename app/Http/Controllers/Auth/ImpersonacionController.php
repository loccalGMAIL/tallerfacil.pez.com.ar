<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Consume los tokens de un solo uso que genera el portal admin
// ("Ingresar como usuario"). Tabla portal_impersonation_tokens.
class ImpersonacionController extends Controller
{
    public function consumir(Request $request, string $token): RedirectResponse
    {
        abort_unless(app()->bound('taller.actual'), 404);

        $taller = app('taller.actual');
        $hash = hash('sha256', $token);

        // Update condicional atómico: si otra request ya lo usó, affected = 0 (anti-replay)
        $consumido = DB::table('portal_impersonation_tokens')
            ->where('token_hash', $hash)
            ->where('taller_id', $taller->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->update(['used_at' => now()]);

        if ($consumido === 0) {
            abort(410, 'El enlace de acceso expiró o ya fue usado.');
        }

        $registro = DB::table('portal_impersonation_tokens')->where('token_hash', $hash)->first();

        $usuario = Usuario::where('id', $registro->usuario_id)
            ->where('activo', true)
            ->where('rol', '!=', 'superadmin')
            ->first();

        if (! $usuario) {
            abort(410, 'El usuario ya no está disponible.');
        }

        Auth::login($usuario); // sin remember: la sesión de soporte no persiste

        $request->session()->regenerate();
        $request->session()->put('impersonado_por_portal', $registro->portal_admin_id);

        activity('taller')
            ->causedBy($usuario)
            ->withProperties(['taller_id' => $taller->id, 'portal_admin_id' => $registro->portal_admin_id, 'ip' => $request->ip()])
            ->log('Sesión de soporte iniciada desde el portal admin');

        return redirect()->route('ordenes.index');
    }
}
