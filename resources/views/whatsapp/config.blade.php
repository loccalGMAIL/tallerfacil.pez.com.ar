@extends('layouts.app')
@section('title', 'WhatsApp — Conexión')

@section('content')
<h1 class="text-xl font-bold text-gray-900 mb-6">WhatsApp — Conexión</h1>

<div class="grid grid-cols-2 gap-6">
    {{-- Estado de conexión + QR --}}
    <div class="bg-white rounded-xl shadow p-5" x-data="{ qr: null, loading: false, error: '' }">
        <h2 class="font-semibold text-gray-700 mb-4">Estado de la sesión</h2>

        <div class="mb-4">
            @if($config && $estado)
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full {{ $estado === 'open' ? 'bg-green-500' : 'bg-red-400' }}"></span>
                <span class="font-medium {{ $estado === 'open' ? 'text-green-700' : 'text-red-600' }}">
                    {{ $estado === 'open' ? 'Conectado' : 'Desconectado' }}
                </span>
            </div>
            @else
            <p class="text-gray-400 text-sm">Configurá la conexión primero.</p>
            @endif
        </div>

        @if($config)
        <div class="flex gap-2 mb-4">
            <button
                @click="loading=true; qr=null; error='';
                    fetch('{{ route('whatsapp.qr') }}', {headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}})
                    .then(r=>r.json()).then(d=>{ loading=false; if(d.ok) qr=d.qr_base64; else error=d.error })"
                :disabled="loading"
                class="bg-gray-800 hover:bg-gray-900 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm">
                <span x-show="!loading">Obtener QR</span>
                <span x-show="loading">Cargando...</span>
            </button>

            <form method="POST" action="{{ route('whatsapp.desconectar') }}" onsubmit="return confirm('¿Desconectar WhatsApp?')">
                @csrf
                <button class="border border-red-300 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm">Desconectar</button>
            </form>
        </div>

        <div x-show="qr" class="text-center">
            <p class="text-sm text-gray-600 mb-2">Escaneá con WhatsApp:</p>
            <img :src="'data:image/png;base64,' + qr" class="mx-auto max-w-xs border rounded">
        </div>
        <p x-show="error" x-text="error" class="text-red-500 text-sm mt-2"></p>
        @endif
    </div>

    {{-- Formulario de configuración --}}
    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-4">Configuración de Evolution API</h2>
        <form method="POST" action="{{ route('whatsapp.config.save') }}">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL base</label>
                    <input type="url" name="url_base" value="{{ old('url_base', $config?->url_base) }}"
                        placeholder="http://localhost:8080"
                        class="w-full border rounded-lg px-3 py-2 text-sm @error('url_base') border-red-400 @enderror">
                    @error('url_base')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                    <input type="text" name="api_key" value="{{ old('api_key', $config?->api_key_masked) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm font-mono"
                        placeholder="tu-api-key">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de instancia</label>
                    <input type="text" name="instancia" value="{{ old('instancia', $config?->instancia) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm"
                        placeholder="taller-principal">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Webhook secret</label>
                    <input type="text" name="webhook_secret" value="{{ old('webhook_secret', $config?->getRawOriginal('webhook_secret')) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm font-mono">
                    <p class="text-xs text-gray-400 mt-1">
                        URL del webhook: <code>{{ config('app.url') }}/api/webhook/evolution</code>
                    </p>
                </div>
            </div>
            <button type="submit" class="mt-4 bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg text-sm font-medium">
                Guardar configuración
            </button>
        </form>
    </div>
</div>

<div class="mt-6 flex gap-4">
    <a href="{{ route('whatsapp.mensajes') }}" class="text-blue-600 hover:underline text-sm">→ Ver log de mensajes</a>
    <a href="{{ route('whatsapp.plantillas') }}" class="text-blue-600 hover:underline text-sm">→ Editar plantillas</a>
    <a href="{{ route('whatsapp.recordatorio') }}" class="text-blue-600 hover:underline text-sm">→ Configurar recordatorios</a>
</div>
@endsection
