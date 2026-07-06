@extends('layouts.superadmin')
@section('title', 'Suscripciones')
@section('content')
<h1 class="text-2xl font-bold mb-6">Suscripciones</h1>

<form method="GET" class="flex flex-wrap gap-3 mb-4">
    <select name="estado" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
        <option value="">Todos los estados</option>
        @foreach(['prueba','activo','vencido','cancelado'] as $e)
        <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
        @endforeach
    </select>
    <select name="taller_id" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
        <option value="">Todos los talleres</option>
        @foreach($talleres as $t)
        <option value="{{ $t->id }}" {{ request('taller_id') == $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg text-sm transition-colors">Filtrar</button>
</form>

<div class="bg-gray-800 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-900 text-gray-400">
            <tr>
                <th class="text-left px-4 py-3">Taller</th>
                <th class="text-left px-4 py-3">Plan</th>
                <th class="text-left px-4 py-3">Estado</th>
                <th class="text-left px-4 py-3">Vencimiento</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
            @forelse($suscripciones as $sus)
            <tr>
                <td class="px-4 py-3">
                    <a href="{{ route('superadmin.talleres.show', $sus->taller) }}"
                       class="text-blue-400 hover:underline">{{ $sus->taller?->nombre ?? '—' }}</a>
                </td>
                <td class="px-4 py-3 text-gray-400">{{ ucfirst($sus->plan) }}</td>
                <td class="px-4 py-3">
                    <span class="text-xs px-2 py-0.5 rounded
                        {{ $sus->estado === 'activo' ? 'bg-green-900 text-green-300' : '' }}
                        {{ $sus->estado === 'prueba' ? 'bg-blue-900 text-blue-300' : '' }}
                        {{ $sus->estado === 'vencido' ? 'bg-red-900 text-red-300' : '' }}
                        {{ $sus->estado === 'cancelado' ? 'bg-gray-700 text-gray-400' : '' }}
                    ">{{ ucfirst($sus->estado) }}</span>
                </td>
                <td class="px-4 py-3 text-gray-400">{{ $sus->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('superadmin.suscripciones.show', $sus->taller) }}"
                       class="text-gray-400 hover:text-white text-xs">Gestionar →</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Sin suscripciones.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $suscripciones->links() }}</div>
@endsection
