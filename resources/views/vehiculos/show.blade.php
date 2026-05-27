@extends('layouts.app')
@section('title', $vehiculo->patente)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('clientes.show', $vehiculo->cliente) }}" class="text-gray-500 hover:text-gray-700 text-sm">← {{ $vehiculo->cliente->nombre }}</a>
        <h1 class="text-xl font-bold text-gray-900">{{ $vehiculo->patente }}</h1>
        <span class="text-gray-500">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} {{ $vehiculo->anio }}</span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('ordenes.create', ['vehiculo_id' => $vehiculo->id]) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm">
            + Nueva OT
        </a>
        @if(auth()->user()->esAdministrador())
        <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm">
            Editar
        </a>
        @endif
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <div class="col-span-1 space-y-4">
        <div class="bg-white rounded-xl shadow p-5 text-sm">
            <h2 class="font-semibold text-gray-700 mb-3 uppercase tracking-wide text-xs">Datos del vehículo</h2>
            <dl class="space-y-2">
                <div><dt class="text-gray-500">Combustible</dt><dd>{{ $vehiculo->combustible ? ucfirst($vehiculo->combustible) : '—' }}</dd></div>
                @if($vehiculo->km_actual)
                <div><dt class="text-gray-500">Km actuales</dt><dd>{{ number_format($vehiculo->km_actual) }} km</dd></div>
                @endif
                @if($vehiculo->fecha_ultimo_service)
                <div><dt class="text-gray-500">Último service</dt><dd>{{ $vehiculo->fecha_ultimo_service->format('d/m/Y') }}</dd></div>
                @endif
                @if($vehiculo->km_ultimo_service)
                <div><dt class="text-gray-500">Km último service</dt><dd>{{ number_format($vehiculo->km_ultimo_service) }} km</dd></div>
                @endif
                @if($vehiculo->notas)
                <div><dt class="text-gray-500">Notas</dt><dd class="text-gray-600">{{ $vehiculo->notas }}</dd></div>
                @endif
            </dl>
        </div>
    </div>

    <div class="col-span-2">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-5 py-4 border-b">
                <h2 class="font-semibold text-gray-800">Historial de órdenes</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 text-gray-600 font-medium">N° Orden</th>
                        <th class="text-left px-4 py-3 text-gray-600 font-medium">Ingreso</th>
                        <th class="text-left px-4 py-3 text-gray-600 font-medium">Estado</th>
                        <th class="text-right px-4 py-3 text-gray-600 font-medium">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ordenes as $orden)
                    @php $badges = ['presupuesto'=>'bg-gray-100 text-gray-700','aprobada'=>'bg-blue-100 text-blue-700','en_proceso'=>'bg-yellow-100 text-yellow-800','finalizada'=>'bg-green-100 text-green-700','entregada'=>'bg-green-700 text-white','cancelada'=>'bg-red-100 text-red-700']; @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono">
                            <a href="{{ route('ordenes.show', $orden) }}" class="hover:text-yellow-600">{{ $orden->numero }}</a>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $orden->fecha_ingreso->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs {{ $badges[$orden->estado] ?? '' }}">
                                {{ ucfirst(str_replace('_',' ',$orden->estado)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">${{ number_format($orden->total_estimado, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Sin órdenes.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4">{{ $ordenes->links() }}</div>
        </div>
    </div>
</div>
@endsection
