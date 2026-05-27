<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        if (!$usuario || !in_array($usuario->rol, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Sin permiso'], 403);
            }

            abort(403, 'No tenés permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
