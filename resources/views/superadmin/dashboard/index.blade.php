@extends('layouts.superadmin')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-gray-800 rounded-xl p-4">
        <div class="text-3xl font-bold text-white">{{ $totalTalleres }}</div>
        <div class="text-gray-400 text-sm mt-1">Total talleres</div>
    </div>
    <div class="bg-gray-800 rounded-xl p-4">
        <div class="text-3xl font-bold text-green-400">{{ $talleresActivos }}</div>
        <div class="text-gray-400 text-sm mt-1">Talleres activos</div>
    </div>
    <div class="bg-gray-800 rounded-xl p-4">
        <div class="text-3xl font-bold text-blue-400">{{ $suscripcionesActivas }}</div>
        <div class="text-gray-400 text-sm mt-1">Suscripciones activas</div>
    </div>
    <div class="bg-gray-800 rounded-xl p-4">
        <div class="text-3xl font-bold text-red-400">{{ $suscripcionesVencidas }}</div>
        <div class="text-gray-400 text-sm mt-1">Suscripciones vencidas</div>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-gray-800 rounded-xl p-5">
        <h2 class="font-semibold text-lg mb-4">Ingresos del mes</h2>
        <div class="text-4xl font-bold text-green-400">
            ${{ number_format($pagosMes, 2, ',', '.') }}
        </div>
        <div class="text-gray-400 text-sm mt-1">ARS — pagos aprobados</div>
    </div>

    <div class="bg-gray-800 rounded-xl p-5">
        <h2 class="font-semibold text-lg mb-4">Últimos talleres</h2>
        <div class="space-y-2">
            @foreach($ultimosTalleres as $taller)
            <div class="flex items-center justify-between text-sm">
                <a href="{{ route('superadmin.talleres.show', $taller) }}"
                   class="text-blue-400 hover:underline">{{ $taller->nombre }}</a>
                <span class="text-gray-500">{{ $taller->subdominio }}</span>
                <span class="{{ $taller->activo ? 'text-green-400' : 'text-red-400' }}">
                    {{ $taller->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            @endforeach
        </div>
        <a href="{{ route('superadmin.talleres.index') }}"
           class="mt-4 inline-block text-sm text-blue-400 hover:underline">
            Ver todos →
        </a>
    </div>
</div>
@endsection
