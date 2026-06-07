@extends('layouts.app')
@section('title', $orden->numero)

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('ordenes.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Órdenes</a>
        <h1 class="text-xl font-bold text-gray-900 font-mono">{{ $orden->numero }}</h1>
        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $orden->estadoBadge() }}">
            {{ $orden->estadoLabel() }}
        </span>
    </div>

    {{-- Botones WA contextuales (admin) --}}
    @if(auth()->user()->esAdministrador())
    <div class="flex flex-wrap items-center gap-2"
         x-data="{ loading:false, msg:'', ok:false,
            enviar(url){ this.loading=true; this.msg='';
                fetch(url,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})
                .then(r=>r.json()).then(d=>{ this.ok=d.ok; this.msg=d.ok?'✓ Mensaje encolado':(d.error||'Error'); this.loading=false; }); } }">

        @if($orden->estado === 'recepcion')
        <button @click="enviar('{{ route('whatsapp.recepcion', $orden) }}')" :disabled="loading"
            class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm">📱 Enviar recepción</button>
        @endif

        @if($orden->estado === 'cotizacion' && $orden->items->count())
        <button @click="enviar('{{ route('whatsapp.presupuesto', $orden) }}')" :disabled="loading"
            class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm">📱 Enviar presupuesto</button>
        @endif

        @if($orden->estado === 'reparacion')
        <button @click="enviar('{{ route('whatsapp.evento', [$orden, 'reparacion']) }}')" :disabled="loading"
            class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm">📱 Avisar en reparación</button>
        @endif

        @if($orden->estado === 'listo')
        <button @click="enviar('{{ route('whatsapp.evento', [$orden, 'listo']) }}')" :disabled="loading"
            class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm">📱 Avisar listo para retirar</button>
        @endif

        @if($orden->estado === 'entregado')
        <button @click="enviar('{{ route('whatsapp.evento', [$orden, 'entregado']) }}')" :disabled="loading"
            class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm">📱 Enviar agradecimiento</button>
        @endif

        <span x-show="msg" x-text="msg" class="text-sm py-2" :class="ok ? 'text-green-700' : 'text-red-600'"></span>
    </div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Columna izquierda: info + ítems --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Info del vehículo/cliente --}}
        <div class="bg-white rounded-xl shadow p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
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
                <div class="sm:col-span-2">
                    <p class="text-gray-500">Descripción del problema</p>
                    <p class="text-gray-700">{{ $orden->descripcion }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Cotización: ítems agrupados --}}
        <div class="bg-white rounded-xl shadow p-5" x-data>
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800">Cotización</h2>
                <div class="flex items-center gap-3">
                    @if($orden->items->count())
                    <a href="{{ route('ordenes.cotizacion.pdf', $orden) }}"
                       class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg">📄 PDF</a>
                    @endif
                    <span class="text-xl font-bold text-gray-900">${{ number_format($orden->total_estimado, 2, ',', '.') }}</span>
                </div>
            </div>

            @php
                $grupos = [
                    'mano_obra' => ['titulo' => 'Servicios', 'items' => $orden->items->where('tipo', 'mano_obra')],
                    'repuesto'  => ['titulo' => 'Repuestos', 'items' => $orden->items->where('tipo', 'repuesto')],
                ];
            @endphp

            @forelse($orden->items as $ignore)
            @break
            @empty
            <p class="py-4 text-center text-gray-400 text-sm mb-4">Sin ítems cargados.</p>
            @endforelse

            @foreach($grupos as $tipo => $grupo)
            @if($grupo['items']->count())
            <div class="mb-4">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-sm font-semibold text-gray-700">{{ $grupo['titulo'] }}
                        <span class="text-gray-400 font-normal">({{ $grupo['items']->count() }})</span></h3>
                    <span class="text-sm font-medium text-gray-600">${{ number_format($grupo['items']->sum('subtotal'), 2, ',', '.') }}</span>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($grupo['items'] as $item)
                        <tr>
                            <td class="py-2">{{ $item->descripcion }}</td>
                            <td class="py-2 text-right text-gray-500 whitespace-nowrap">${{ number_format($item->precio_unitario, 2, ',', '.') }} × {{ $item->cantidad }}</td>
                            <td class="py-2 text-right font-medium whitespace-nowrap">${{ number_format($item->subtotal, 2, ',', '.') }}</td>
                            @if($orden->estaAbierta())
                            <td class="py-2 text-right pl-2">
                                <form method="POST" action="{{ route('ordenes.items.destroy', [$orden, $item]) }}"
                                    onsubmit="return confirm('¿Eliminar este ítem?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-600 text-xs">✕</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            @endforeach

            {{-- Agregar ítem --}}
            @if($orden->estaAbierta())
            <form method="POST" action="{{ route('ordenes.items.store', $orden) }}"
                x-data="{
                    cant: 1, precio: 0, subtotal: 0, descripcion: '', tipo: 'mano_obra',
                    servicios: {{ Illuminate\Support\Js::from($servicios->mapWithKeys(fn($s) => [$s->id => ['descripcion' => $s->nombre, 'tipo' => $s->tipo, 'precio' => (float) $s->precio]])) }},
                    aplicar(id) {
                        if (!id || !this.servicios[id]) return;
                        const s = this.servicios[id];
                        this.descripcion = s.descripcion;
                        this.tipo = s.tipo;
                        this.precio = s.precio;
                        this.subtotal = (this.cant * this.precio).toFixed(2);
                    }
                }"
                class="border-t pt-4">
                @csrf
                <p class="text-xs font-medium text-gray-600 mb-2 uppercase tracking-wide">Agregar ítem</p>

                @if($servicios->count())
                <div class="mb-2">
                    <select @change="aplicar($event.target.value); $event.target.value=''"
                        class="w-full border rounded px-2 py-2 text-sm bg-yellow-50 border-yellow-200 text-gray-700">
                        <option value="">⚡ Cargar desde catálogo de servicios…</option>
                        @foreach($servicios as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }} — ${{ number_format($s->precio, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                    <div class="col-span-2">
                        <input type="text" name="descripcion" x-model="descripcion" required placeholder="Descripción"
                            class="w-full border rounded px-2 py-2 text-sm">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <select name="tipo" x-model="tipo" class="w-full border rounded px-2 py-2 text-sm">
                            <option value="mano_obra">Mano de obra</option>
                            <option value="repuesto">Repuesto</option>
                        </select>
                    </div>
                    <div>
                        <input type="number" name="cantidad" x-model="cant" @input="subtotal=(cant*precio).toFixed(2)"
                            step="0.01" min="0.01" value="1" placeholder="Cant."
                            class="w-full border rounded px-2 py-2 text-sm">
                    </div>
                    <div>
                        <input type="number" name="precio_unitario" x-model="precio" @input="subtotal=(cant*precio).toFixed(2)"
                            step="0.01" min="0" placeholder="Precio unit."
                            class="w-full border rounded px-2 py-2 text-sm">
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

        {{-- Tareas a realizar --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-800 mb-3">Tareas a realizar</h2>
            <ul class="space-y-1 mb-3">
                @forelse($orden->tareas as $tarea)
                <li class="flex items-center gap-2 group">
                    <form method="POST" action="{{ route('ordenes.tareas.toggle', [$orden, $tarea]) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-5 h-5 rounded border flex items-center justify-center text-xs
                            {{ $tarea->completada ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300 text-transparent hover:border-gray-400' }}">✓</button>
                    </form>
                    <span class="flex-1 text-sm {{ $tarea->completada ? 'line-through text-gray-400' : 'text-gray-700' }}">{{ $tarea->descripcion }}</span>
                    @if($orden->estaAbierta())
                    <form method="POST" action="{{ route('ordenes.tareas.destroy', [$orden, $tarea]) }}"
                        onsubmit="return confirm('¿Eliminar esta tarea?')">
                        @csrf @method('DELETE')
                        <button class="text-red-300 hover:text-red-600 text-xs opacity-0 group-hover:opacity-100">✕</button>
                    </form>
                    @endif
                </li>
                @empty
                <li class="text-sm text-gray-400 py-2">Sin tareas cargadas.</li>
                @endforelse
            </ul>
            @if($orden->estaAbierta())
            <form method="POST" action="{{ route('ordenes.tareas.store', $orden) }}" class="flex gap-2 border-t pt-3">
                @csrf
                <input type="text" name="descripcion" required placeholder="Nueva tarea…"
                    class="flex-1 border rounded px-2 py-1.5 text-sm">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded text-sm">+ Agregar</button>
            </form>
            @endif
        </div>

        {{-- Fotos --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-800 mb-3">Fotos</h2>
            @if($orden->fotos->count())
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 mb-3">
                @foreach($orden->fotos as $foto)
                <div class="relative group aspect-square">
                    <a href="{{ $foto->url }}" target="_blank">
                        <img src="{{ $foto->url }}" alt="{{ $foto->descripcion }}"
                            class="w-full h-full object-cover rounded-lg border border-gray-200">
                    </a>
                    <span class="absolute bottom-1 left-1 text-[9px] bg-black/60 text-white px-1 rounded capitalize">{{ $foto->tipo }}</span>
                    @if($orden->estaAbierta())
                    <form method="POST" action="{{ route('ordenes.fotos.destroy', [$orden, $foto]) }}"
                        onsubmit="return confirm('¿Eliminar esta foto?')"
                        class="absolute top-1 right-1 opacity-0 group-hover:opacity-100">
                        @csrf @method('DELETE')
                        <button class="bg-red-600 text-white rounded-full w-5 h-5 text-xs leading-none">✕</button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 mb-3">Sin fotos cargadas.</p>
            @endif

            @if($orden->estaAbierta())
            <form method="POST" action="{{ route('ordenes.fotos.store', $orden) }}" enctype="multipart/form-data"
                class="border-t pt-3 flex flex-wrap items-center gap-2">
                @csrf
                <select name="tipo" class="border rounded px-2 py-1.5 text-sm">
                    <option value="recepcion">Recepción</option>
                    <option value="patente">Patente</option>
                    <option value="trabajo">Trabajo</option>
                </select>
                {{-- capture="environment" abre la cámara trasera en el celular --}}
                <input type="file" name="foto" accept="image/*" capture="environment" required
                    class="text-sm flex-1 min-w-[160px]">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-3 py-1.5 rounded text-sm">📷 Subir</button>
            </form>
            @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>
    </div>

    {{-- Columna derecha: acciones + historial --}}
    <div class="lg:col-span-1 space-y-5">

        {{-- Cambio de estado --}}
        @if($transiciones && auth()->user()->esAdministrador() || (auth()->user()->rol === 'mecanico' && count($transiciones)))
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-800 mb-3">Cambiar estado</h2>
            @foreach($transiciones as $nuevoEstado)
            <form method="POST" action="{{ route('ordenes.estado', $orden) }}" class="mb-2"
                @if($nuevoEstado === 'entregado') x-data="{ show: false }" @endif>
                @csrf
                <input type="hidden" name="estado" value="{{ $nuevoEstado }}">

                @if($nuevoEstado === 'entregado')
                <div x-show="show" class="mb-2">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="actualizar_service" value="1"> Actualizar fecha/km de último service
                    </label>
                </div>
                <button type="button"
                    @click="show = !show; if(show) $nextTick(()=>$el.form.submit())"
                    class="w-full px-3 py-2 rounded-lg text-sm font-medium bg-gray-800 hover:bg-gray-900 text-white">
                    → {{ \App\Models\Orden::ESTADO_LABELS[$nuevoEstado] ?? ucfirst($nuevoEstado) }}
                </button>
                @else
                <button type="submit"
                    class="w-full px-3 py-2 rounded-lg text-sm font-medium {{ $nuevoEstado === 'cancelado' ? 'border border-red-300 text-red-600 hover:bg-red-50' : 'bg-gray-800 hover:bg-gray-900 text-white' }}">
                    → {{ \App\Models\Orden::ESTADO_LABELS[$nuevoEstado] ?? ucfirst($nuevoEstado) }}
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

        {{-- Enviar mensaje manual (admin) --}}
        @if(auth()->user()->esAdministrador())
        <div class="bg-white rounded-xl shadow p-5"
             x-data="{
                texto: '', loading:false, msg:'', ok:false,
                guardados: {{ Illuminate\Support\Js::from($guardados->mapWithKeys(fn($g) => [$g->id => $g->texto])) }},
                cargar(id){ if(id && this.guardados[id]) this.texto = this.guardados[id]; },
                enviar(){ if(!this.texto.trim()) return; this.loading=true; this.msg='';
                    fetch('{{ route('whatsapp.manual', $orden) }}',{method:'POST',
                        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json','Accept':'application/json'},
                        body: JSON.stringify({ texto: this.texto })})
                    .then(r=>r.json()).then(d=>{ this.ok=d.ok; this.msg=d.ok?'✓ Mensaje encolado':(d.error||'Error'); this.loading=false; if(d.ok) this.texto=''; }); }
             }">
            <h2 class="font-semibold text-gray-800 mb-3">Enviar mensaje</h2>
            @if($guardados->count())
            <select @change="cargar($event.target.value); $event.target.value=''"
                class="w-full border rounded-lg px-2 py-2 text-sm mb-2 bg-green-50 border-green-200">
                <option value="">Usar mensaje guardado…</option>
                @foreach($guardados as $g)
                <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                @endforeach
            </select>
            @endif
            <textarea x-model="texto" rows="3" placeholder="Escribí un mensaje para el cliente…"
                class="w-full border rounded-lg px-3 py-2 text-sm mb-2"></textarea>
            <div class="flex items-center justify-between">
                <span x-show="msg" x-text="msg" class="text-xs" :class="ok ? 'text-green-700' : 'text-red-600'"></span>
                <button @click="enviar()" :disabled="loading"
                    class="ml-auto bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm">📱 Enviar</button>
            </div>
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
