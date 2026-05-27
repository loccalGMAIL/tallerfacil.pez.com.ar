@extends('layouts.app')
@section('title', 'Órdenes de Trabajo')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-gray-900">Órdenes de Trabajo</h1>
    @if(auth()->user()->esAdministrador())
    <a href="{{ route('ordenes.create') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
        + Nueva orden
    </a>
    @endif
</div>

{{-- Filtros --}}
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <select name="estado" class="border rounded-lg px-3 py-2 text-sm">
        <option value="">Todos los estados</option>
        @foreach(['presupuesto','aprobada','en_proceso','finalizada','entregada','cancelada'] as $e)
        <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $e)) }}</option>
        @endforeach
    </select>
    @if(auth()->user()->esAdministrador())
    <select name="mecanico_id" class="border rounded-lg px-3 py-2 text-sm">
        <option value="">Todos los mecánicos</option>
        @foreach($mecanicos as $m)
        <option value="{{ $m->id }}" {{ request('mecanico_id') == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
        @endforeach
    </select>
    @endif
    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="border rounded-lg px-3 py-2 text-sm">
    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="border rounded-lg px-3 py-2 text-sm">
    <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Filtrar</button>
    @if(request()->hasAny(['estado','mecanico_id','fecha_desde','fecha_hasta']))
    <a href="{{ route('ordenes.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm">Limpiar</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">N° Orden</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Vehículo</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Cliente</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Ingreso</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Estado</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Total</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Mecánico</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($ordenes as $orden)
            @php
            $badges = [
                'presupuesto' => 'bg-gray-100 text-gray-700',
                'aprobada'    => 'bg-blue-100 text-blue-700',
                'en_proceso'  => 'bg-yellow-100 text-yellow-800',
                'finalizada'  => 'bg-green-100 text-green-700',
                'entregada'   => 'bg-green-700 text-white',
                'cancelada'   => 'bg-red-100 text-red-700',
            ];
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono font-medium">
                    <a href="{{ route('ordenes.show', $orden) }}" class="hover:text-yellow-600">{{ $orden->numero }}</a>
                </td>
                <td class="px-4 py-3">{{ $orden->vehiculo->patente }} <span class="text-gray-500">{{ $orden->vehiculo->marca }}</span></td>
                <td class="px-4 py-3">
                    <a href="{{ route('clientes.show', $orden->vehiculo->cliente) }}" class="hover:underline">
                        {{ $orden->vehiculo->cliente->nombre }}
                    </a>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $orden->fecha_ingreso->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $badges[$orden->estado] ?? '' }}">
                        {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                    </span>
                </td>
                <td class="px-4 py-3 font-medium">${{ number_format($orden->total_estimado, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $orden->mecanico?->nombre ?? '—' }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('ordenes.show', $orden) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Sin órdenes.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $ordenes->links() }}</div>
@endsection
