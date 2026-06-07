@extends('layouts.app')
@section('title', $cliente->nombre)

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('clientes.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Clientes</a>
        <h1 class="text-xl font-bold text-gray-900">{{ $cliente->nombre }}</h1>
        @unless($cliente->activo)
        <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded">Inactivo</span>
        @endunless
    </div>
    @if(auth()->user()->esAdministrador())
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('vehiculos.create', ['cliente_id' => $cliente->id]) }}" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm">
            + Vehículo
        </a>
        <a href="{{ route('clientes.edit', $cliente) }}" class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm">
            Editar
        </a>
        @if($cliente->activo)
        <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" onsubmit="return confirm('¿Desactivar este cliente?')">
            @csrf @method('DELETE')
            <button class="border border-red-200 text-red-500 hover:bg-red-50 px-4 py-2 rounded-lg text-sm">Desactivar</button>
        </form>
        @endif
    </div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Datos del cliente --}}
    <div class="lg:col-span-1 bg-white rounded-xl shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">Datos</h2>
        <dl class="space-y-2 text-sm">
            <div><dt class="text-gray-500">Documento</dt><dd>{{ $cliente->tipo_doc }}: {{ $cliente->nro_doc ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Teléfono</dt><dd>{{ $cliente->telefono_display }}</dd></div>
            @if($cliente->email)
            <div><dt class="text-gray-500">Email</dt><dd>{{ $cliente->email }}</dd></div>
            @endif
            @if($cliente->direccion)
            <div><dt class="text-gray-500">Dirección</dt><dd>{{ $cliente->direccion }}</dd></div>
            @endif
            @if($cliente->notas)
            <div><dt class="text-gray-500">Notas</dt><dd class="text-gray-600">{{ $cliente->notas }}</dd></div>
            @endif
        </dl>
    </div>

    {{-- Vehículos --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">
                Vehículos ({{ $vehiculos->count() }})
            </h2>
            @forelse($vehiculos as $vehiculo)
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 py-2 border-b last:border-0">
                <div>
                    <a href="{{ route('vehiculos.show', $vehiculo) }}" class="font-medium text-gray-800 hover:text-yellow-600">
                        {{ $vehiculo->patente }}
                    </a>
                    <span class="text-gray-500 text-sm ml-2">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} {{ $vehiculo->anio }}</span>
                </div>
                <div class="flex flex-wrap gap-3 text-xs text-gray-500">
                    @if($vehiculo->km_actual)
                    <span>{{ number_format($vehiculo->km_actual) }} km</span>
                    @endif
                    @if($vehiculo->fecha_ultimo_service)
                    <span>Último service: {{ $vehiculo->fecha_ultimo_service->format('d/m/Y') }}</span>
                    @endif
                    <a href="{{ route('ordenes.create', ['vehiculo_id' => $vehiculo->id]) }}" class="text-yellow-600 hover:underline">Nueva OT</a>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm">Sin vehículos registrados.</p>
            @endforelse
        </div>

        {{-- Últimos mensajes WA --}}
        @if($mensajes->count())
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3 text-sm uppercase tracking-wide">Últimos mensajes WA</h2>
            @foreach($mensajes as $msg)
            <div class="flex items-center justify-between py-1.5 text-sm border-b last:border-0">
                <span class="text-gray-600 truncate max-w-xs">{{ Str::limit($msg->contenido, 60) }}</span>
                <div class="flex gap-2 text-xs text-gray-400 ml-4 shrink-0">
                    <span class="capitalize">{{ $msg->tipo }}</span>
                    <span class="px-1.5 py-0.5 rounded text-xs {{ match($msg->estado_entrega) {
                        'leido' => 'bg-blue-100 text-blue-700',
                        'entregado' => 'bg-green-100 text-green-700',
                        'fallido' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-600'
                    } }}">{{ $msg->estado_entrega }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
