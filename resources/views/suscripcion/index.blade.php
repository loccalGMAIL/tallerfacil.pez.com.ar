@extends('layouts.app')
@section('title', 'Suscripción')
@section('content')

<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-2">Suscripción</h1>
    <p class="text-gray-500 text-sm mb-6">Estado de tu plan en TallerFácil.</p>

    {{-- Estado actual --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-lg">Plan actual</h2>
            @if($suscripcion)
            <span class="text-xs px-2 py-0.5 rounded font-medium
                {{ $suscripcion->estado === 'activo' ? 'bg-green-100 text-green-700' : '' }}
                {{ $suscripcion->estado === 'prueba' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $suscripcion->estado === 'vencido' ? 'bg-red-100 text-red-700' : '' }}
                {{ $suscripcion->estado === 'cancelado' ? 'bg-gray-100 text-gray-500' : '' }}
            ">{{ ucfirst($suscripcion->estado) }}</span>
            @else
            <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500">Sin suscripción</span>
            @endif
        </div>

        @if($suscripcion)
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Plan</dt>
                <dd class="font-medium">{{ ucfirst($suscripcion->plan) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Vencimiento</dt>
                <dd class="font-medium">{{ $suscripcion->fecha_vencimiento?->format('d/m/Y') ?? 'Sin vencimiento' }}</dd>
            </div>
        </dl>

        @if(in_array($suscripcion->estado, ['activo', 'prueba']))
        <p class="mt-4 text-sm text-green-600 font-medium">Tu cuenta está activa.</p>
        @else
        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
            Tu suscripción está {{ $suscripcion->estado === 'vencido' ? 'vencida' : 'cancelada' }}.
            Para seguir usando TallerFácil, renová tu plan.
        </div>
        @endif
        @else
        <p class="text-sm text-gray-500">No tenés una suscripción activa. Elegí un plan para continuar.</p>
        @endif
    </div>

    {{-- Planes --}}
    @if(!$suscripcion || !in_array($suscripcion->estado, ['activo', 'prueba']))
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-lg mb-4">Elegí tu plan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach([
                ['plan' => 'basico',   'label' => 'Básico',   'precio' => '$5.000/mes',  'features' => ['Órdenes ilimitadas', 'Hasta 2 usuarios', 'PDF de cotización']],
                ['plan' => 'estandar', 'label' => 'Estándar', 'precio' => '$10.000/mes', 'features' => ['Todo lo del Básico', 'Hasta 5 usuarios', 'WhatsApp integrado']],
                ['plan' => 'premium',  'label' => 'Premium',  'precio' => '$18.000/mes', 'features' => ['Todo lo del Estándar', 'Usuarios ilimitados', 'Soporte prioritario']],
            ] as $opcion)
            <div class="border rounded-xl p-4 {{ $opcion['plan'] === 'estandar' ? 'border-yellow-400 ring-2 ring-yellow-400' : 'border-gray-200' }}">
                <div class="font-semibold mb-1">{{ $opcion['label'] }}</div>
                <div class="text-2xl font-bold text-yellow-600 mb-3">{{ $opcion['precio'] }}</div>
                <ul class="text-sm text-gray-600 space-y-1 mb-4">
                    @foreach($opcion['features'] as $feature)
                    <li>✓ {{ $feature }}</li>
                    @endforeach
                </ul>
                <form method="POST" action="{{ route('suscripcion.checkout') }}">
                    @csrf
                    <input type="hidden" name="plan" value="{{ $opcion['plan'] }}">
                    <button type="submit"
                        class="w-full {{ $opcion['plan'] === 'estandar' ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-800' }} font-semibold py-2 rounded-lg text-sm transition-colors">
                        Elegir plan
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
