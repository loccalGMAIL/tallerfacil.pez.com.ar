<?php

use App\Http\Controllers\Api\MercadoPagoWebhookController;
use App\Http\Controllers\Api\RecordatorioController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Webhook de Evolution API — validado por middleware
Route::post('/webhook/evolution', [WebhookController::class, 'evolution'])
    ->middleware('evolution.webhook')
    ->name('webhook.evolution');

// Webhook de MercadoPago
Route::post('/webhook/mercadopago', MercadoPagoWebhookController::class)
    ->name('webhook.mercadopago');

// Endpoints para n8n — token estático en env RECORDATORIO_TOKEN
Route::middleware('recordatorio.token')->group(function () {
    Route::get('/vehiculos/para-recordatorio', [RecordatorioController::class, 'paraRecordatorio'])
        ->name('api.recordatorio.vehiculos');
    Route::post('/recordatorios/log', [RecordatorioController::class, 'log'])
        ->name('api.recordatorio.log');
});
