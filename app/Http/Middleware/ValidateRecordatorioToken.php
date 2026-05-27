<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateRecordatorioToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->header('X-Recordatorio-Token');
        $expected = config('app.recordatorio_token');

        if (!$token || !$expected || !hash_equals($expected, $token)) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        return $next($request);
    }
}
