<?php

namespace App\Services;

use App\Models\WaConfig;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionApiService
{
    private ?WaConfig $config;

    public function __construct()
    {
        $this->config = WaConfig::instancia();
    }

    public function sendText(string $numero, string $texto): array
    {
        $this->ensureConfigured();

        $response = Http::withHeaders(['apikey' => $this->config->getRawOriginal('api_key')])
            ->timeout(15)
            ->post("{$this->config->url_base}/message/sendText/{$this->config->instancia}", [
                'number' => $numero,
                'text'   => $texto,
            ]);

        if ($response->failed()) {
            throw new Exception("Evolution API error: " . $response->status() . " " . $response->body());
        }

        return $response->json();
    }

    public function getConnectionState(): array
    {
        $this->ensureConfigured();

        try {
            $response = Http::withHeaders(['apikey' => $this->config->getRawOriginal('api_key')])
                ->timeout(10)
                ->get("{$this->config->url_base}/instance/connectionState/{$this->config->instancia}");

            return $response->json() ?? [];
        } catch (Exception $e) {
            Log::warning('Evolution API estado no disponible: ' . $e->getMessage());
            return ['state' => 'close'];
        }
    }

    public function connect(): array
    {
        $this->ensureConfigured();

        $response = Http::withHeaders(['apikey' => $this->config->getRawOriginal('api_key')])
            ->timeout(15)
            ->get("{$this->config->url_base}/instance/connect/{$this->config->instancia}");

        if ($response->failed()) {
            throw new Exception("No se pudo conectar a Evolution API: " . $response->body());
        }

        return $response->json();
    }

    public function logout(): array
    {
        $this->ensureConfigured();

        $response = Http::withHeaders(['apikey' => $this->config->getRawOriginal('api_key')])
            ->timeout(10)
            ->delete("{$this->config->url_base}/instance/logout/{$this->config->instancia}");

        return $response->json() ?? [];
    }

    public function estaConfigurado(): bool
    {
        return $this->config
            && !empty($this->config->url_base)
            && !empty($this->config->getRawOriginal('api_key'))
            && !empty($this->config->instancia);
    }

    private function ensureConfigured(): void
    {
        if (!$this->estaConfigurado()) {
            throw new Exception('Evolution API no está configurada. Configurá la conexión en Ajustes → WhatsApp.');
        }
    }
}
