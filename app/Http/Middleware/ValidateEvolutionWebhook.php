<?php

namespace App\Http\Middleware;

use App\Models\Taller;
use App\Models\WaConfig;
use App\Scopes\TallerScope;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateEvolutionWebhook
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = $request->header('X-Evolution-Secret') ?? $request->header('apikey');

        if (!$secret) {
            return response()->json(['error' => 'Webhook no autorizado'], 401);
        }

        // Buscar la configuración por webhook_secret sin filtro de taller
        // para poder identificar a qué taller pertenece este webhook
        $config = WaConfig::withoutGlobalScope(TallerScope::class)
            ->whereNotNull('webhook_secret')
            ->where('webhook_secret', '!=', '')
            ->get()
            ->first(fn ($c) => hash_equals($c->getRawOriginal('webhook_secret'), $secret));

        if (!$config) {
            return response()->json(['error' => 'Webhook no autorizado'], 401);
        }

        // Establecer el contexto de taller para que los modelos funcionen correctamente
        $taller = Taller::find($config->taller_id);
        if ($taller) {
            app()->instance('taller.actual', $taller);
            view()->share('tallerActual', $taller);
        }

        return $next($request);
    }
}
