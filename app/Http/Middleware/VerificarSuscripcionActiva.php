<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarSuscripcionActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!app()->bound('taller.actual')) {
            return $next($request);
        }

        $taller = app('taller.actual');

        if (!$taller->tieneSuscripcionActiva()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Suscripción vencida o inactiva.'], 402);
            }
            return redirect()->route('suscripcion.index');
        }

        return $next($request);
    }
}
