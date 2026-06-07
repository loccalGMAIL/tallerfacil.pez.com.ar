@extends('layouts.app')
@section('title', 'Configurar Recordatorios')

@section('content')
@include('whatsapp._nav')

<form method="POST" action="{{ route('whatsapp.recordatorio.save') }}" class="max-w-xl space-y-5">
    @csrf

    {{-- Recordatorios de service --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Recordatorios de service</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div class="sm:col-span-2">
                <label class="flex items-center gap-2 text-sm font-medium">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" {{ $config?->activo ? 'checked' : '' }} class="rounded">
                    Recordatorios de service activos
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Umbral de meses</label>
                <input type="number" name="umbral_meses" value="{{ old('umbral_meses', $config?->umbral_meses ?? 6) }}"
                    min="1" class="w-full border rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Enviar si hace N meses del último service.</p>
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
    </div>

    {{-- Envío automático al cambiar de estado --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-800 mb-1">Envío automático al cambiar de estado</h2>
        <p class="text-xs text-gray-400 mb-4">Cuando una orden pasa a estos estados, se envía sola la plantilla correspondiente (sin tener que tocar el botón).</p>

        <div class="space-y-2">
            @foreach([
                'auto_recepcion'  => ['Recepción',  'Al ingresar el vehículo'],
                'auto_reparacion' => ['Reparación', 'Al empezar a trabajar'],
                'auto_listo'      => ['Listo',       'Cuando está listo para retirar'],
                'auto_entregado'  => ['Entregado',   'Al entregar (agradecimiento)'],
            ] as $campo => $info)
            <label class="flex items-center justify-between gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer">
                <span>
                    <span class="text-sm font-medium text-gray-800">{{ $info[0] }}</span>
                    <span class="block text-xs text-gray-400">{{ $info[1] }}</span>
                </span>
                <input type="hidden" name="{{ $campo }}" value="0">
                <input type="checkbox" name="{{ $campo }}" value="1" {{ old($campo, $config?->{$campo}) ? 'checked' : '' }}
                    class="rounded text-yellow-500 focus:ring-yellow-500 w-5 h-5">
            </label>
            @endforeach
        </div>
    </div>

    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium">
        Guardar configuración
    </button>
</form>
@endsection
