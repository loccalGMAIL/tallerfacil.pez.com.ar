@extends('layouts.app')
@section('title', $orden->numero)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('ordenes.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Órdenes</a>
        <h1 class="text-xl font-bold text-gray-900 font-mono">{{ $orden->numero }}</h1>
        @php
        $badges = ['presupuesto'=>'bg-gray-100 text-gray-700','aprobada'=>'bg-blue-100 text-blue-700','en_proceso'=>'bg-yellow-100 text-yellow-800','finalizada'=>'bg-green-100 text-green-700','entregada'=>'bg-green-700 text-white','cancelada'=>'bg-red-100 text-red-700'];
        @endphp
        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $badges[$orden->estado] ?? '' }}">
            {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
        </span>
    </div>

    {{-- Botones WA (admin) --}}
    @if(auth()->user()->esAdministrador() && $orden->estaAbierta())
    <div class="flex gap-2" x-data="{ loading: false, msg: '' }">
        @if($orden->estado === 'presupuesto' && $orden->items->count())
        <button
            @click="loading=true; fetch('{{ route('whatsapp.presupuesto', $orden) }}', {method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}}).then(r=>r.json()).then(d=>{ msg=d.ok?'✓ Presupuesto enviado':d.error; loading=false })"
            :disabled="loading"
            class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm">
            📱 Enviar presupuesto
        </button>
        @endif
        <button
            @click="loading=true; fetch('{{ route('whatsapp.recepcion', $orden) }}', {method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}}).then(r=>r.json()).then(d=>{ msg=d.ok?'✓ Recepción enviada':d.error; loading=false })"
            :disabled="loading"
            class="bg-green-500 hover:bg-green-600 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm">
            📱 Enviar recepción
        </button>
        <span x-show="msg" x-text="msg" class="text-sm py-2 text-green-700"></span>
    </div>
    @endif
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Columna izquierda: info + ítems --}}
    <div class="col-span-2 space-y-5">

        {{-- Info del vehículo/cliente --}}
        <div class="bg-white rounded-xl shadow p-5">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 mb-1">Vehículo</p>
                    <p class="font-bold text-lg">{{ $orden->vehiculo->patente }}</p>
                    <p class="text-gray-700">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} {{ $orden->vehiculo->anio }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Cliente</p>
                    <a href="{{ route('clientes.show', $orden->vehiculo->cliente) }}" class="font-semibold hover:text-yellow-600">
                        {{ $orden->vehiculo->cliente->nombre }}
                    </a>
                    <p class="text-gray-600">{{ $orden->vehiculo->cliente->telefono_display }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Ingreso</p>
                    <p>{{ $orden->fecha_ingreso->format('d/m/Y') }}
                    @if($orden->km_ingreso) — {{ number_format($orden->km_ingreso) }} km @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Mecánico</p>
                    <p>{{ $orden->mecanico?->nombre ?? 'Sin asignar' }}</p>
                </div>
                @if($orden->descripcion)
                <div class="col-span-2">
                    <p class="text-gray-500">Descripción del problema</p>
                    <p class="text-gray-700">{{ $orden->descripcion }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Ítems de la orden --}}
        <div class="bg-white rounded-xl shadow p-5" x-data>
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800">Ítems de la orden</h2>
                <span class="text-xl font-bold text-gray-900">${{ number_format($orden->total_estimado, 2, ',', '.') }}</span>
            </div>

            <table class="w-full text-sm mb-4">
                <thead class="border-b text-gray-500">
                    <tr>
                        <th class="text-left py-2">Descripción</th>
                        <th class="text-left py-2">Tipo</th>
                        <th class="text-right py-2">Cant.</th>
                        <th class="text-right py-2">P. Unit.</th>
                        <th class="text-right py-2">Subtotal</th>
                        @if($orden->estaAbierta()) <th class="py-2"></th> @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orden->items as $item)
                    <tr>
                        <td class="py-2">{{ $item->descripcion }}</td>
                        <td class="py-2 text-gray-500 capitalize">{{ str_replace('_', ' ', $item->tipo) }}</td>
                        <td class="py-2 text-right">{{ $item->cantidad }}</td>
                        <td class="py-2 text-right">${{ number_format($item->precio_unitario, 2, ',', '.') }}</td>
                        <td class="py-2 text-right font-medium">${{ number_format($item->subtotal, 2, ',', '.') }}</td>
                        @if($orden->estaAbierta())
                        <td class="py-2 text-right">
                            <form method="POST" action="{{ route('ordenes.items.destroy', [$orden, $item]) }}"
                                onsubmit="return confirm('¿Eliminar este ítem?')">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600 text-xs">✕</button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-4 text-center text-gray-400">Sin ítems cargados.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Agregar ítem --}}
            @if($orden->estaAbierta())
            <form method="POST" action="{{ route('ordenes.items.store', $orden) }}"
                x-data="{ cant: 1, precio: 0, subtotal: 0 }"
                class="border-t pt-4">
                @csrf
                <p class="text-xs font-medium text-gray-600 mb-2 uppercase tracking-wide">Agregar ítem</p>
                <div class="grid grid-cols-5 gap-2">
                    <div class="col-span-2">
                        <input type="text" name="descripcion" required placeholder="Descripción"
                            class="w-full border rounded px-2 py-1.5 text-sm">
                    </div>
                    <div>
                        <select name="tipo" class="w-full border rounded px-2 py-1.5 text-sm">
                            <option value="mano_obra">Mano de obra</option>
                            <option value="repuesto">Repuesto</option>
                        </select>
                    </div>
                    <div>
                        <input type="number" name="cantidad" x-model="cant" @input="subtotal=(cant*precio).toFixed(2)"
                            step="0.01" min="0.01" value="1" placeholder="Cant."
                            class="w-full border rounded px-2 py-1.5 text-sm">
                    </div>
                    <div>
                        <input type="number" name="precio_unitario" x-model="precio" @input="subtotal=(cant*precio).toFixed(2)"
                            step="0.01" min="0" placeholder="Precio unit."
                            class="w-full border rounded px-2 py-1.5 text-sm">
                    </div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-sm text-gray-600">Subtotal: <strong x-text="'$' + subtotal"></strong></span>
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-1.5 rounded text-sm">
                        + Agregar
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>

    {{-- Columna derecha: acciones + historial --}}
    <div class="col-span-1 space-y-5">

        {{-- Cambio de estado --}}
        @if($transiciones && auth()->user()->esAdministrador() || (auth()->user()->rol === 'mecanico' && count($transiciones)))
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-800 mb-3">Cambiar estado</h2>
            @foreach($transiciones as $nuevoEstado)
            <form method="POST" action="{{ route('ordenes.estado', $orden) }}" class="mb-2"
                @if($nuevoEstado === 'entregada') x-data="{ show: false }" @endif>
                @csrf
                <input type="hidden" name="estado" value="{{ $nuevoEstado }}">

                @if($nuevoEstado === 'entregada')
                <div x-show="show" class="mb-2">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="actualizar_service" value="1"> Actualizar fecha/km de último service
                    </label>
                </div>
                <button type="@if($nuevoEstado === 'entregada') button @else submit @endif"
                    @if($nuevoEstado === 'entregada') @click="show = !show; if(show) $nextTick(()=>$el.form.submit())" @endif
                    class="w-full px-3 py-2 rounded-lg text-sm font-medium {{ $nuevoEstado === 'cancelada' ? 'border border-red-300 text-red-600 hover:bg-red-50' : 'bg-gray-800 hover:bg-gray-900 text-white' }}">
                    → {{ ucfirst(str_replace('_', ' ', $nuevoEstado)) }}
                </button>
                @else
                <button type="submit"
                    class="w-full px-3 py-2 rounded-lg text-sm font-medium {{ $nuevoEstado === 'cancelada' ? 'border border-red-300 text-red-600 hover:bg-red-50' : 'bg-gray-800 hover:bg-gray-900 text-white' }}">
                    → {{ ucfirst(str_replace('_', ' ', $nuevoEstado)) }}
                </button>
                @endif
            </form>
            @endforeach
        </div>
        @endif

        {{-- Asignar mecánico --}}
        @if(auth()->user()->esAdministrador() && $orden->estaAbierta())
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-800 mb-3">Mecánico</h2>
            <form method="POST" action="{{ route('ordenes.mecanico', $orden) }}">
                @csrf
                <select name="mecanico_id" class="w-full border rounded-lg px-3 py-2 text-sm mb-2">
                    <option value="">Sin asignar</option>
                    @foreach($mecanicos as $m)
                    <option value="{{ $m->id }}" {{ $orden->mecanico_id == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
                    @endforeach
                </select>
                <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-sm">Guardar</button>
            </form>
        </div>
        @endif

        {{-- Historial de estados --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-800 mb-3">Historial</h2>
            <ol class="space-y-2">
                @foreach($orden->historial as $h)
                <li class="flex gap-3 text-sm">
                    <div class="mt-0.5 w-2 h-2 rounded-full bg-gray-400 shrink-0 mt-1.5"></div>
                    <div>
                        <span class="font-medium capitalize">{{ str_replace('_', ' ', $h->estado) }}</span>
                        <span class="text-gray-400 text-xs ml-1">{{ $h->fecha_hora->format('d/m/Y H:i') }}</span>
                        @if($h->usuario) <span class="text-gray-500 text-xs">· {{ $h->usuario->nombre }}</span> @endif
                        @if($h->notas) <p class="text-gray-500 text-xs">{{ $h->notas }}</p> @endif
                    </div>
                </li>
                @endforeach
            </ol>
        </div>

        {{-- Mensajes WA --}}
        @if($orden->waMensajes->count())
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-800 mb-3">Mensajes WA</h2>
            @foreach($orden->waMensajes as $msg)
            <div class="text-xs py-1.5 border-b last:border-0">
                <div class="flex justify-between">
                    <span class="font-medium capitalize">{{ $msg->tipo }}</span>
                    <span class="text-gray-400">{{ $msg->fecha_hora->format('d/m H:i') }}</span>
                </div>
                <span class="px-1.5 py-0.5 rounded mt-0.5 inline-block {{ match($msg->estado_entrega) {
                    'leido' => 'bg-blue-100 text-blue-700',
                    'entregado' => 'bg-green-100 text-green-700',
                    'fallido' => 'bg-red-100 text-red-700',
                    default => 'bg-gray-100 text-gray-600'
                } }}">{{ $msg->estado_entrega }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
