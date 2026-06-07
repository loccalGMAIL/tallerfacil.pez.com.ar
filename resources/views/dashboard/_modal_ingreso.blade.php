{{-- Modal Ingresar vehículo / Cotización rápida — usa el scope Alpine del padre (modal, modo, clienteNuevo) --}}
<div x-show="modal" x-cloak
     class="fixed inset-0 z-50 flex sm:items-center sm:justify-center"
     @keydown.escape.window="modal = false">

    {{-- Fondo --}}
    <div class="absolute inset-0 bg-black/50" @click="modal = false"></div>

    {{-- Panel: full-screen en móvil, card centrada en desktop --}}
    <div class="relative bg-white w-full h-full sm:h-auto sm:max-w-lg sm:rounded-2xl shadow-xl
                flex flex-col sm:max-h-[90vh] overflow-hidden"
         x-show="modal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

        {{-- Cabecera fija --}}
        <div class="flex items-center justify-between px-4 py-3 border-b shrink-0">
            <h2 class="font-bold text-gray-900 flex items-center gap-2">
                <span x-show="modo === 'recepcion'">🚗 Ingresar vehículo</span>
                <span x-show="modo === 'cotizacion'">⚡ Cotización rápida</span>
            </h2>
            <button type="button" @click="modal = false" class="text-gray-400 hover:text-gray-700 text-2xl leading-none w-8 h-8">&times;</button>
        </div>

        <form method="POST" action="{{ route('recepcion.store') }}" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            <input type="hidden" name="modo" :value="modo">

            {{-- Cuerpo scrollable --}}
            <div class="flex-1 overflow-y-auto px-4 py-4 space-y-5">

                {{-- Selector de modo --}}
                <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 rounded-lg text-sm">
                    <button type="button" @click="modo = 'recepcion'"
                        :class="modo === 'recepcion' ? 'bg-white shadow text-gray-900' : 'text-gray-500'"
                        class="rounded-md py-2 font-medium transition-colors">Solo ingresar</button>
                    <button type="button" @click="modo = 'cotizacion'"
                        :class="modo === 'cotizacion' ? 'bg-white shadow text-gray-900' : 'text-gray-500'"
                        class="rounded-md py-2 font-medium transition-colors">Ingresar y cotizar</button>
                </div>

                {{-- Cliente --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cliente</label>
                        <button type="button" @click="clienteNuevo = !clienteNuevo" class="text-xs text-blue-600 hover:underline">
                            <span x-show="!clienteNuevo">+ Nuevo cliente</span>
                            <span x-show="clienteNuevo">Buscar existente</span>
                        </button>
                    </div>

                    {{-- Existente --}}
                    <div x-show="!clienteNuevo">
                        <select name="cliente_id" class="w-full border rounded-lg px-3 py-2.5 text-base">
                            <option value="">Seleccionar cliente…</option>
                            @foreach($clientes as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }} — {{ $c->telefono_display }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Nuevo --}}
                    <div x-show="clienteNuevo" class="space-y-2">
                        <input type="text" name="cliente_nombre" placeholder="Nombre y apellido"
                            :disabled="!clienteNuevo"
                            class="w-full border rounded-lg px-3 py-2.5 text-base">
                        <input type="tel" name="cliente_telefono" placeholder="Teléfono (ej. 11 5000-0000)"
                            :disabled="!clienteNuevo"
                            class="w-full border rounded-lg px-3 py-2.5 text-base">
                    </div>
                </div>

                {{-- Vehículo --}}
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-2">Vehículo</label>
                    <input type="text" name="patente" placeholder="Patente" required
                        style="text-transform: uppercase"
                        class="w-full border rounded-lg px-3 py-2.5 text-base font-mono mb-2">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="marca" placeholder="Marca" class="w-full border rounded-lg px-3 py-2.5 text-base">
                        <input type="text" name="modelo" placeholder="Modelo" class="w-full border rounded-lg px-3 py-2.5 text-base">
                        <input type="number" name="anio" placeholder="Año" min="1900" max="{{ now()->year + 1 }}" class="w-full border rounded-lg px-3 py-2.5 text-base">
                        <input type="number" name="km_ingreso" placeholder="Km de ingreso" min="0" class="w-full border rounded-lg px-3 py-2.5 text-base">
                    </div>
                </div>
            </div>

            {{-- Footer fijo con botón único (cambia según modo) --}}
            <div class="border-t px-4 py-3 shrink-0 bg-white">
                <button type="submit"
                    class="w-full text-white font-semibold rounded-lg py-3 text-base transition-colors"
                    :class="modo === 'cotizacion' ? 'bg-purple-600 hover:bg-purple-700' : 'bg-yellow-500 hover:bg-yellow-600'">
                    <span x-show="modo === 'recepcion'">Ingresar al tablero</span>
                    <span x-show="modo === 'cotizacion'">Crear cotización →</span>
                </button>
            </div>
        </form>
    </div>
</div>
