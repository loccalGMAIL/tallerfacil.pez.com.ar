@extends('layouts.app')
@section('title', 'Panel')

@section('content')
@php use App\Models\Orden; @endphp

<div x-data="{ modal: false, modo: 'recepcion', clienteNuevo: {{ $clientes->isEmpty() ? 'true' : 'false' }} }"
     @abrir-ingreso.window="modo = $event.detail; modal = true">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <h1 class="text-xl font-bold text-gray-900">Panel Operativo</h1>

        <div class="flex items-center gap-2">
            {{-- Toggle Tablero/Calendario --}}
            <div class="inline-flex rounded-lg border border-gray-200 bg-white p-0.5 text-sm">
                <a href="{{ route('dashboard', ['vista' => 'tablero']) }}"
                   class="px-3 py-1.5 rounded-md {{ $vista === 'tablero' ? 'bg-gray-900 text-white' : 'text-gray-600' }}">Tablero</a>
                <a href="{{ route('dashboard', ['vista' => 'calendario']) }}"
                   class="px-3 py-1.5 rounded-md {{ $vista === 'calendario' ? 'bg-gray-900 text-white' : 'text-gray-600' }}">Calendario</a>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600 text-lg">$</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($kpis['ingresos'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500">Ingresos del mes (entregados)</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 text-lg">📄</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($kpis['total_cotizado'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500">Total cotizado del mes</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 text-lg">✓</div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $kpis['vehiculos_entregados'] }}</p>
                <p class="text-xs text-gray-500">Vehículos entregados</p>
            </div>
        </div>
    </div>

    @if($vista === 'tablero')
    {{-- Tablero Kanban --}}
    @php
        $colColumna = ['recepcion'=>'border-blue-400','cotizacion'=>'border-purple-400','reparacion'=>'border-yellow-400','listo'=>'border-green-400'];
        $colHeader  = ['recepcion'=>'text-blue-600','cotizacion'=>'text-purple-600','reparacion'=>'text-yellow-700','listo'=>'text-green-600'];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4" id="kanban">
        @foreach(Orden::ESTADOS_TABLERO as $estado)
        @php $items = $columnas[$estado]; @endphp
        <div class="bg-gray-50 rounded-xl border-t-4 {{ $colColumna[$estado] }} p-3">
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="font-semibold text-sm {{ $colHeader[$estado] }}">{{ Orden::ESTADO_LABELS[$estado] }}</h2>
                <span class="text-xs font-medium text-gray-400 bg-white rounded-full px-2 py-0.5">{{ $items->count() }}</span>
            </div>

            {{-- Contenedor sortable --}}
            <div class="space-y-2 min-h-[80px] kanban-col" data-estado="{{ $estado }}">
                @foreach($items as $orden)
                @php $siguiente = collect(Orden::TRANSICIONES[$orden->estado] ?? [])->first(fn($e) => $e !== 'cancelado'); @endphp
                <div class="kanban-card bg-white rounded-lg shadow-sm border border-gray-100 p-3 cursor-grab active:cursor-grabbing"
                     data-orden="{{ $orden->id }}">
                    <a href="{{ route('ordenes.show', $orden) }}" class="block">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-mono text-xs font-semibold border border-gray-300 rounded px-1.5 py-0.5">{{ $orden->vehiculo->patente }}</span>
                            <span class="text-[10px] text-gray-400">{{ $orden->fecha_ingreso->diffForHumans(null, true) }}</span>
                        </div>
                        <p class="font-semibold text-sm text-gray-800 leading-tight">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $orden->vehiculo->cliente->nombre }}</p>
                        @if($orden->total_estimado > 0)
                        <p class="text-xs text-gray-700 font-medium mt-1">${{ number_format($orden->total_estimado, 0, ',', '.') }}</p>
                        @endif
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @else
    {{-- Vista calendario --}}
    @include('dashboard._calendario', ['cal' => $calendario])
    @endif

    {{-- Botones de acción flotantes (mobile-first) --}}
    <div class="fixed right-4 bottom-20 md:bottom-6 z-40 flex flex-col items-end gap-3">
        <button type="button" @click="$dispatch('abrir-ingreso', 'cotizacion')"
            class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white rounded-full shadow-lg pl-3 pr-4 py-2.5 text-sm font-medium">
            ⚡ <span class="hidden sm:inline">Cotización rápida</span>
        </button>
        <button type="button" @click="$dispatch('abrir-ingreso', 'recepcion')"
            class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-full shadow-lg pl-4 pr-5 py-3 text-sm font-semibold">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 17H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h11l4 4v4a2 2 0 0 1-2 2h-1"/>
                <path d="M14 7H8L6 11h10l-2-4Z"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>
            </svg>
            <span>Ingresar<span class="hidden sm:inline"> vehículo</span></span>
        </button>
    </div>

    {{-- Modal de ingreso --}}
    @include('dashboard._modal_ingreso')
</div>

{{-- SortableJS para drag-and-drop --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    document.querySelectorAll('.kanban-col').forEach(function (col) {
        new Sortable(col, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'opacity-40',
            onAdd: function (evt) {
                const ordenId = evt.item.getAttribute('data-orden');
                const nuevoEstado = evt.to.getAttribute('data-estado');
                fetch(`/ordenes/${ordenId}/mover`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ estado: nuevoEstado }),
                })
                .then(r => r.json())
                .then(d => { if (!d.ok) { alert(d.error || 'No se pudo mover'); location.reload(); } })
                .catch(() => location.reload());
            },
        });
    });
});
</script>
@endsection
