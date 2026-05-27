<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\WaConfig;
use App\Models\WaMensaje;
use App\Models\WaPlantilla;
use App\Models\WaRecordatorioConfig;
use App\Services\EvolutionApiService;
use App\Services\WhatsAppService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppController extends Controller
{
    public function __construct(
        private WhatsAppService    $whatsApp,
        private EvolutionApiService $evolution,
    ) {}

    // ── Mensajes manuales ────────────────────────────────────

    public function enviarPresupuesto(Orden $orden): JsonResponse
    {
        try {
            $mensaje = $this->whatsApp->enviarPresupuesto($orden);

            return response()->json(['ok' => true, 'wa_mensaje_id' => $mensaje->id]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function enviarRecepcion(Orden $orden): JsonResponse
    {
        try {
            $mensaje = $this->whatsApp->enviarRecepcion($orden);

            return response()->json(['ok' => true, 'wa_mensaje_id' => $mensaje->id]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }

    // ── Log de mensajes ──────────────────────────────────────

    public function mensajes(Request $request): View
    {
        $mensajes = WaMensaje::with(['cliente', 'orden'])
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->tipo, fn ($q) => $q->where('tipo', $request->tipo))
            ->when($request->estado_entrega, fn ($q) => $q->where('estado_entrega', $request->estado_entrega))
            ->latest('fecha_hora')
            ->paginate(30)
            ->withQueryString();

        return view('whatsapp.mensajes', compact('mensajes'));
    }

    // ── Configuración ────────────────────────────────────────

    public function config(): View
    {
        $config = WaConfig::instancia();
        $estado = null;

        if ($config && $this->evolution->estaConfigurado()) {
            try {
                $raw    = $this->evolution->getConnectionState();
                $estado = data_get($raw, 'instance.state') ?? data_get($raw, 'state') ?? 'close';
            } catch (Exception) {
                $estado = 'error';
            }
        }

        return view('whatsapp.config', compact('config', 'estado'));
    }

    public function saveConfig(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'url_base'       => ['required', 'url'],
            'api_key'        => ['required', 'string'],
            'instancia'      => ['required', 'string', 'max:100'],
            'webhook_secret' => ['required', 'string', 'min:8'],
        ]);

        WaConfig::updateOrCreate(['id' => 1], $datos);

        return back()->with('success', 'Configuración guardada.');
    }

    public function qr(): JsonResponse
    {
        try {
            $resultado = $this->evolution->connect();

            return response()->json([
                'ok'        => true,
                'qr_base64' => data_get($resultado, 'base64') ?? data_get($resultado, 'qrcode.base64'),
            ]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function desconectar(): JsonResponse
    {
        try {
            $this->evolution->logout();
            WaConfig::where('id', 1)->update(['estado_conexion' => 'desconectado']);

            return response()->json(['ok' => true]);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }

    // ── Recordatorio config ──────────────────────────────────

    public function recordatorioConfig(): View
    {
        $config = WaRecordatorioConfig::instancia();

        return view('whatsapp.recordatorio', compact('config'));
    }

    public function saveRecordatorioConfig(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'umbral_meses'        => ['nullable', 'integer', 'min:1'],
            'umbral_km'           => ['nullable', 'integer', 'min:100'],
            'ventana_minima_dias' => ['required', 'integer', 'min:1'],
            'activo'              => ['required', 'boolean'],
            'tope_diario'         => ['required', 'integer', 'min:1', 'max:500'],
            'delay_min_seg'       => ['required', 'integer', 'min:5'],
            'delay_max_seg'       => ['required', 'integer', 'min:10'],
        ]);

        WaRecordatorioConfig::updateOrCreate(['id' => 1], array_merge($datos, ['updated_at' => now()]));

        return back()->with('success', 'Configuración de recordatorios guardada.');
    }

    // ── Plantillas ───────────────────────────────────────────

    public function plantillas(): View
    {
        $plantillas = WaPlantilla::orderBy('tipo')->get();

        return view('whatsapp.plantillas', compact('plantillas'));
    }

    public function updatePlantilla(Request $request, WaPlantilla $plantilla): RedirectResponse
    {
        $request->validate([
            'texto'  => ['required', 'string'],
            'activo' => ['required', 'boolean'],
        ]);

        $plantilla->update($request->only('texto', 'activo'));

        return back()->with('success', 'Plantilla actualizada.');
    }
}
