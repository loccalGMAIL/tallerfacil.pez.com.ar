<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->esSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Solo superadministradores.'], 403);
            }
            abort(403, 'Solo superadministradores.');
        }

        return $next($request);
    }
}
