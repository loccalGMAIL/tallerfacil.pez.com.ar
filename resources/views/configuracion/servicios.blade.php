@extends('layouts.app')
@section('title', 'Configuración · Servicios')

@section('content')
@include('configuracion._nav')

<div class="max-w-3xl space-y-5">

    {{-- Alta de servicio --}}
    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="font-semibold text-gray-800 mb-3">Agregar servicio</h2>
        <form method="POST" action="{{ route('configuracion.servicios.store') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">
                <input type="text" name="nombre" required placeholder="Nombre (ej. Cambio de aceite)"
                    class="sm:col-span-5 border rounded-lg px-3 py-2.5 text-sm @error('nombre') border-red-400 @enderror">
                <select name="tipo" class="sm:col-span-3 border rounded-lg px-3 py-2.5 text-sm">
                    <option value="mano_obra">Mano de obra</option>
                    <option value="repuesto">Repuesto</option>
                </select>
                <input type="number" name="precio" step="0.01" min="0" required placeholder="Precio"
                    class="sm:col-span-2 border rounded-lg px-3 py-2.5 text-sm">
                <button type="submit" class="sm:col-span-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium">+ Agregar</button>
            </div>
            @error('nombre')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            @error('precio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </form>
    </div>

    {{-- Listado --}}
    <div class="bg-white rounded-xl shadow divide-y divide-gray-100">
        @forelse($servicios as $servicio)
        <div x-data="{ editar: false }" class="p-4">
            {{-- Vista --}}
            <div x-show="!editar" class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-medium text-gray-800 truncate">
                        {{ $servicio->nombre }}
                        @unless($servicio->activo)<span class="text-xs text-gray-400">(inactivo)</span>@endunless
                    </p>
                    <p class="text-xs text-gray-500">
                        <span class="capitalize">{{ str_replace('_', ' ', $servicio->tipo) }}</span>
                        · ${{ number_format($servicio->precio, 2, ',', '.') }}
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button @click="editar = true" class="text-blue-600 hover:underline text-sm">Editar</button>
                    <form method="POST" action="{{ route('configuracion.servicios.destroy', $servicio) }}"
                        onsubmit="return confirm('¿Eliminar este servicio?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-600 text-sm">Eliminar</button>
                    </form>
                </div>
            </div>

            {{-- Edición --}}
            <form x-show="editar" x-cloak method="POST" action="{{ route('configuracion.servicios.update', $servicio) }}">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                    <input type="text" name="nombre" value="{{ $servicio->nombre }}" required
                        class="sm:col-span-4 border rounded-lg px-3 py-2 text-sm">
                    <select name="tipo" class="sm:col-span-2 border rounded-lg px-3 py-2 text-sm">
                        <option value="mano_obra" {{ $servicio->tipo === 'mano_obra' ? 'selected' : '' }}>Mano de obra</option>
                        <option value="repuesto" {{ $servicio->tipo === 'repuesto' ? 'selected' : '' }}>Repuesto</option>
                    </select>
                    <input type="number" name="precio" step="0.01" min="0" value="{{ $servicio->precio }}" required
                        class="sm:col-span-2 border rounded-lg px-3 py-2 text-sm">
                    <label class="sm:col-span-2 flex items-center gap-1.5 text-sm text-gray-600">
                        <input type="checkbox" name="activo" value="1" {{ $servicio->activo ? 'checked' : '' }}> Activo
                    </label>
                    <div class="sm:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 bg-gray-800 hover:bg-gray-900 text-white px-3 py-2 rounded-lg text-sm">Guardar</button>
                        <button type="button" @click="editar = false" class="text-gray-500 text-sm px-2">✕</button>
                    </div>
                </div>
                <input type="text" name="descripcion" value="{{ $servicio->descripcion }}" placeholder="Descripción (opcional)"
                    class="w-full border rounded-lg px-3 py-2 text-sm mt-2">
            </form>
        </div>
        @empty
        <p class="p-6 text-center text-gray-400 text-sm">Todavía no cargaste servicios.</p>
        @endforelse
    </div>
</div>
@endsection
