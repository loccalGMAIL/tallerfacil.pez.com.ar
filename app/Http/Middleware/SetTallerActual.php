<?php

namespace App\Http\Middleware;

use App\Models\Taller;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTallerActual
{
    public function handle(Request $request, Closure $next): Response
    {
        $host       = $request->getHost();
        $baseDomain = config('app.base_domain', 'tallerfacil.com.ar');
        $subdominio = str($host)->before('.' . $baseDomain)->value();

        // El subdominio 'admin' no corresponde a un taller sino al panel superadmin
        if ($subdominio === 'admin' || $subdominio === $host) {
            return $next($request);
        }

        $taller = Taller::where('subdominio', $subdominio)
            ->where('activo', true)
            ->first();

        if (!$taller) {
            abort(404, 'Taller no encontrado o inactivo.');
        }

        app()->instance('taller.actual', $taller);
        view()->share('tallerActual', $taller);

        return $next($request);
    }
}
