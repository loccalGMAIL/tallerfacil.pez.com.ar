<?php

namespace App\Http\Middleware;

use App\Models\WaConfig;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateEvolutionWebhook
{
    public function handle(Request $request, Closure $next): Response
    {
        $config = WaConfig::instancia();
        $secret = $request->header('X-Evolution-Secret') ?? $request->header('apikey');

        if (!$config || !$secret || !hash_equals($config->getRawOriginal('webhook_secret'), $secret)) {
            return response()->json(['error' => 'Webhook no autorizado'], 401);
        }

        return $next($request);
    }
}
