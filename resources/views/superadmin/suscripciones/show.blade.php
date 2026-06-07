@extends('layouts.superadmin')
@section('title', 'Suscripciones — ' . $taller->nombre)
@section('content')
<a href="{{ route('superadmin.suscripciones.index') }}" class="text-gray-500 hover:text-gray-300 text-sm">← Suscripciones</a>
<h1 class="text-2xl font-bold mt-1 mb-6">{{ $taller->nombre }}</h1>

@foreach($suscripciones as $sus)
<div class="bg-gray-800 rounded-xl p-5 mb-4">
    <div class="flex items-center justify-between mb-3">
        <div>
            <span class="font-semibold">{{ ucfirst($sus->plan) }}</span>
            <span class="ml-2 text-xs px-2 py-0.5 rounded
                {{ $sus->estado === 'activo' ? 'bg-green-900 text-green-300' : '' }}
                {{ $sus->estado === 'prueba' ? 'bg-blue-900 text-blue-300' : '' }}
                {{ $sus->estado === 'vencido' ? 'bg-red-900 text-red-300' : '' }}
                {{ $sus->estado === 'cancelado' ? 'bg-gray-700 text-gray-400' : '' }}
            ">{{ ucfirst($sus->estado) }}</span>
        </div>
        <div class="flex gap-2">
            @if(!in_array($sus->estado, ['activo']))
            <form method="POST" action="{{ route('superadmin.suscripciones.activar', $sus) }}">
                @csrf
                <select name="plan" class="bg-gray-700 border border-gray-600 rounded px-2 py-1 text-xs text-white mr-1">
                    @foreach(['basico','estandar','premium'] as $p)
                    <option value="{{ $p }}" {{ $sus->plan === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
                <input type="date" name="fecha_vencimiento"
                       class="bg-gray-700 border border-gray-600 rounded px-2 py-1 text-xs text-white mr-1">
                <button type="submit" class="bg-green-800 hover:bg-green-700 text-green-200 px-3 py-1 rounded text-xs transition-colors">Activar</button>
            </form>
            @endif
            @if($sus->estado !== 'cancelado')
            <form method="POST" action="{{ route('superadmin.suscripciones.cancelar', $sus) }}">
                @csrf
                <button type="submit" class="bg-red-900 hover:bg-red-800 text-red-300 px-3 py-1 rounded text-xs transition-colors">Cancelar</button>
            </form>
            @endif
        </div>
    </div>

    <dl class="grid grid-cols-3 gap-3 text-sm text-gray-400 mb-4">
        <div><dt>Inicio</dt><dd class="text-white">{{ $sus->fecha_inicio->format('d/m/Y') }}</dd></div>
        <div><dt>Vencimiento</dt><dd class="text-white">{{ $sus->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</dd></div>
        <div><dt>MP Preapproval</dt><dd class="text-white font-mono text-xs">{{ $sus->mp_preapproval_id ?? '—' }}</dd></div>
    </dl>

    @if($sus->pagos->isNotEmpty())
    <h3 class="text-sm font-semibold text-gray-300 mb-2">Pagos</h3>
    <table class="w-full text-xs">
        <thead class="text-gray-500">
            <tr>
                <th class="text-left pb-1">Fecha</th>
                <th class="text-left pb-1">Monto</th>
                <th class="text-left pb-1">Estado</th>
                <th class="text-left pb-1">MP ID</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
            @foreach($sus->pagos as $pago)
            <tr>
                <td class="py-1 text-gray-400">{{ $pago->fecha_pago?->format('d/m/Y') ?? '—' }}</td>
                <td class="py-1">${{ number_format($pago->monto, 2, ',', '.') }}</td>
                <td class="py-1">
                    <span class="{{ $pago->estado === 'aprobado' ? 'text-green-400' : 'text-red-400' }}">
                        {{ ucfirst($pago->estado) }}
                    </span>
                </td>
                <td class="py-1 font-mono text-gray-500">{{ $pago->mp_payment_id ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="text-xs text-gray-500">Sin pagos registrados.</p>
    @endif
</div>
@endforeach

@if($suscripciones->isEmpty())
<div class="bg-gray-800 rounded-xl p-8 text-center text-gray-500">Sin suscripciones para este taller.</div>
@endif
@endsection
