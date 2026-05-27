@extends('layouts.app')
@section('title', 'Configurar Recordatorios')

@section('content')
<h1 class="text-xl font-bold text-gray-900 mb-6">Recordatorios de Service</h1>

<div class="max-w-xl bg-white rounded-xl shadow p-6">
    <form method="POST" action="{{ route('whatsapp.recordatorio.save') }}">
        @csrf

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="col-span-2">
                <label class="flex items-center gap-2 text-sm font-medium">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" {{ $config?->activo ? 'checked' : '' }} class="rounded">
                    Envíos automáticos activos
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Umbral de meses</label>
                <input type="number" name="umbral_meses" value="{{ old('umbral_meses', $config?->umbral_meses ?? 6) }}"
                    min="1" class="w-full border rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Enviar si hace N meses del último service.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Umbral de km</label>
                <input type="number" name="umbral_km" value="{{ old('umbral_km', $config?->umbral_km ?? 10000) }}"
                    min="100" class="w-full border rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Enviar si recorrió N km desde el último service.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tope diario de mensajes</label>
                <input type="number" name="tope_diario" value="{{ old('tope_diario', $config?->tope_diario ?? 50) }}"
                    min="1" max="500" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ventana mínima (días)</label>
                <input type="number" name="ventana_minima_dias" value="{{ old('ventana_minima_dias', $config?->ventana_minima_dias ?? 30) }}"
                    min="1" class="w-full border rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">No reenviar si ya se contactó en los últimos N días.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Delay mínimo (seg.)</label>
                <input type="number" name="delay_min_seg" value="{{ old('delay_min_seg', $config?->delay_min_seg ?? 30) }}"
                    min="5" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Delay máximo (seg.)</label>
                <input type="number" name="delay_max_seg" value="{{ old('delay_max_seg', $config?->delay_max_seg ?? 90) }}"
                    min="10" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg text-sm font-medium">
            Guardar configuración
        </button>
    </form>
</div>
@endsection
