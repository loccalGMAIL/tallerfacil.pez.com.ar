<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\Suscripcion;
use App\Models\Taller;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\PreApproval\PreApprovalClient;
use MercadoPago\Client\PreApprovalPlan\PreApprovalPlanClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
    }

    public function crearPreapproval(Taller $taller, string $plan, string $backUrl): ?string
    {
        try {
            $client = new PreApprovalClient();

            $precios = ['basico' => 5000, 'estandar' => 10000, 'premium' => 18000];
            $monto = $precios[$plan] ?? 5000;

            $response = $client->create([
                'reason'            => ucfirst($plan) . ' — TallerFácil',
                'auto_recurring'    => [
                    'frequency'       => 1,
                    'frequency_type'  => 'months',
                    'transaction_amount' => $monto,
                    'currency_id'     => 'ARS',
                ],
                'back_url'          => $backUrl,
                'payer_email'       => $taller->email,
            ]);

            return $response->init_point ?? null;
        } catch (\Throwable $e) {
            Log::error('MercadoPago crearPreapproval error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function cancelarPreapproval(string $preapprovalId): bool
    {
        try {
            $client = new PreApprovalClient();
            $client->update($preapprovalId, ['status' => 'cancelled']);
            return true;
        } catch (\Throwable $e) {
            Log::error('MercadoPago cancelarPreapproval error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function procesarWebhook(array $payload): void
    {
        $tipo  = $payload['type'] ?? null;
        $datos = $payload['data'] ?? [];
        $id    = $datos['id'] ?? null;

        if (!$id) return;

        if ($tipo === 'subscription_preapproval') {
            $this->procesarCambioSuscripcion($id);
        } elseif ($tipo === 'payment') {
            $this->procesarPago($id);
        }
    }

    private function procesarCambioSuscripcion(string $preapprovalId): void
    {
        $suscripcion = Suscripcion::withoutGlobalScopes()
            ->where('mp_preapproval_id', $preapprovalId)
            ->first();

        if (!$suscripcion) return;

        try {
            $client   = new PreApprovalClient();
            $response = $client->get($preapprovalId);

            $estado = match($response->status ?? '') {
                'authorized', 'active' => 'activo',
                'cancelled', 'paused'  => 'cancelado',
                default                => null,
            };

            if ($estado) {
                $suscripcion->update(['estado' => $estado]);
            }
        } catch (\Throwable $e) {
            Log::error('MercadoPago procesarCambioSuscripcion error', ['error' => $e->getMessage()]);
        }
    }

    private function procesarPago(string $paymentId): void
    {
        try {
            $client   = new \MercadoPago\Client\Payment\PaymentClient();
            $response = $client->get((int) $paymentId);

            $preapprovalId = $response->metadata->preapproval_id ?? null;

            $suscripcion = $preapprovalId
                ? Suscripcion::withoutGlobalScopes()->where('mp_preapproval_id', $preapprovalId)->first()
                : null;

            if (!$suscripcion) return;

            $estado = match($response->status ?? '') {
                'approved'            => 'aprobado',
                'rejected', 'refunded' => 'rechazado',
                default               => 'pendiente',
            };

            Pago::withoutGlobalScopes()->updateOrCreate(
                ['mp_payment_id' => (string) $paymentId],
                [
                    'suscripcion_id' => $suscripcion->id,
                    'taller_id'      => $suscripcion->taller_id,
                    'monto'          => $response->transaction_amount ?? 0,
                    'estado'         => $estado,
                    'fecha_pago'     => now(),
                    'detalle_json'   => json_encode((array) $response),
                ]
            );
        } catch (\Throwable $e) {
            Log::error('MercadoPago procesarPago error', ['error' => $e->getMessage()]);
        }
    }
}
