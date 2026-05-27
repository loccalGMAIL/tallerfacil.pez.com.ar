<?php

namespace App\Jobs;

use App\Models\WaMensaje;
use App\Services\EvolutionApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class EnviarMensajeWhatsAppJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public array $backoff = [60, 300, 900];

    public function __construct(
        private readonly int    $waMensajeId,
        private readonly string $numero,
        private readonly string $texto,
    ) {}

    public function handle(EvolutionApiService $evolution): void
    {
        $mensaje = WaMensaje::findOrFail($this->waMensajeId);
        $mensaje->increment('intentos');

        $resultado = $evolution->sendText($this->numero, $this->texto);

        $mensaje->update([
            'estado_entrega'       => 'enviado',
            'evolution_message_id' => data_get($resultado, 'key.id'),
        ]);
    }

    public function failed(Throwable $exception): void
    {
        WaMensaje::where('id', $this->waMensajeId)->update([
            'estado_entrega' => 'fallido',
            'error_detalle'  => $exception->getMessage(),
        ]);
    }
}
