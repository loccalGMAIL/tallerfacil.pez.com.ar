@extends('layouts.app')
@section('title', 'Vehículos')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-gray-900">Vehículos</h1>
    @if(auth()->user()->esAdministrador())
    <a href="{{ route('vehiculos.create') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Nuevo vehículo</a>
    @endif
</div>

<form method="GET" class="mb-4 flex flex-col sm:flex-row gap-2">
    <div class="flex gap-2">
        <input type="text" name="patente" value="{{ request('patente') }}" placeholder="Patente..."
            class="border rounded-lg px-3 py-2 text-sm w-full sm:w-36 focus:ring-2 focus:ring-yellow-500">
        <input type="text" name="marca" value="{{ request('marca') }}" placeholder="Marca..."
            class="border rounded-lg px-3 py-2 text-sm w-full sm:w-36">
    </div>
    <div class="flex gap-2">
        <button type="submit" class="flex-1 sm:flex-none bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Buscar</button>
        @if(request()->hasAny(['patente','marca']))
        <a href="{{ route('vehiculos.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm">Limpiar</a>
        @endif
    </div>
</form>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm min-w-[480px]">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Patente</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Marca / Modelo</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 hidden sm:table-cell">Cliente</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 hidden md:table-cell">Km</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 hidden md:table-cell">Último service</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($vehiculos as $vehiculo)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono font-medium">
                    <a href="{{ route('vehiculos.show', $vehiculo) }}" class="hover:text-yellow-600">{{ $vehiculo->patente }}</a>
                </td>
                <td class="px-4 py-3">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} <span class="text-gray-400">{{ $vehiculo->anio }}</span></td>
                <td class="px-4 py-3 hidden sm:table-cell">
                    <a href="{{ route('clientes.show', $vehiculo->cliente) }}" class="hover:underline">{{ $vehiculo->cliente->nombre }}</a>
                </td>
                <td class="px-4 py-3 text-gray-600 hidden md:table-cell whitespace-nowrap">{{ $vehiculo->km_actual ? number_format($vehiculo->km_actual) . ' km' : '—' }}</td>
                <td class="px-4 py-3 text-gray-600 hidden md:table-cell whitespace-nowrap">{{ $vehiculo->fecha_ultimo_service?->format('d/m/Y') ?? '—' }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('vehiculos.show', $vehiculo) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Sin vehículos.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $vehiculos->links() }}</div>
@endsection
