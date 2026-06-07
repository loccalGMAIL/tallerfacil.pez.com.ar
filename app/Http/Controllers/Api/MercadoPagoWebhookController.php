<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MercadoPagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    public function __invoke(Request $request, MercadoPagoService $mp): JsonResponse
    {
        $secret = config('mercadopago.webhook_secret');

        if ($secret) {
            $xSignature  = $request->header('x-signature', '');
            $xRequestId  = $request->header('x-request-id', '');
            $queryId     = $request->query('data.id', $request->input('data.id', ''));

            $manifest = "id:{$queryId};request-id:{$xRequestId};ts:" . explode(',ts=', $xSignature)[1] ?? '';
            $ts       = explode(',ts=', $xSignature)[1] ?? '';
            $v1Part   = explode('v1=', explode(',', $xSignature)[1] ?? '')[1] ?? '';
            $manifest = "id:{$queryId};request-id:{$xRequestId};ts:{$ts}";

            if (!hash_equals(hash_hmac('sha256', $manifest, $secret), $v1Part)) {
                Log::warning('MercadoPago webhook: firma inválida');
                return response()->json(['ok' => false], 400);
            }
        }

        try {
            $mp->procesarWebhook($request->all());
        } catch (\Throwable $e) {
            Log::error('MercadoPago webhook error', ['error' => $e->getMessage()]);
        }

        return response()->json(['ok' => true]);
    }
}
