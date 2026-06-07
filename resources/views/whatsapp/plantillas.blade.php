@extends('layouts.app')
@section('title', 'Plantillas WhatsApp')

@section('content')
@include('whatsapp._nav')

<p class="text-sm text-gray-500 mb-6">Variables disponibles: <code class="bg-gray-100 px-1 rounded">{nombre}</code> <code class="bg-gray-100 px-1 rounded">{marca}</code> <code class="bg-gray-100 px-1 rounded">{modelo}</code> <code class="bg-gray-100 px-1 rounded">{patente}</code> <code class="bg-gray-100 px-1 rounded">{total}</code> <code class="bg-gray-100 px-1 rounded">{numero_orden}</code> <code class="bg-gray-100 px-1 rounded">{fecha_ingreso}</code> <code class="bg-gray-100 px-1 rounded">{items_lista}</code></p>

<h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Plantillas de evento</h2>
<p class="text-xs text-gray-400 mb-4">Se envían desde la orden según su estado (recepción, presupuesto, reparación, listo, entregado).</p>

<div class="space-y-6">
    @foreach($plantillas as $plantilla)
    <div class="bg-white rounded-xl shadow p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $plantilla->tipo) }}</h2>
            <span class="text-xs px-2 py-0.5 rounded {{ $plantilla->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $plantilla->activo ? 'Activa' : 'Inactiva' }}
            </span>
        </div>
        <form method="POST" action="{{ route('whatsapp.plantillas.update', $plantilla) }}">
            @csrf @method('PUT')
            <textarea name="texto" rows="5"
                class="w-full border rounded-lg px-3 py-2 text-sm font-mono mb-3">{{ old('texto', $plantilla->texto) }}</textarea>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" {{ $plantilla->activo ? 'checked' : '' }} class="rounded"> Activa
                </label>
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-1.5 rounded text-sm">Guardar</button>
            </div>
        </form>
    </div>
    @endforeach
</div>

{{-- Mensajes guardados (biblioteca personalizada para envíos manuales) --}}
<h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mt-10 mb-3">Mensajes guardados</h2>
<p class="text-xs text-gray-400 mb-4">Mensajes libres que podés enviar a mano desde una orden. Acepta las mismas variables.</p>

<div class="max-w-3xl space-y-4">
    {{-- Alta --}}
    <div class="bg-white rounded-xl shadow p-5">
        <form method="POST" action="{{ route('whatsapp.guardados.store') }}">
            @csrf
            <input type="text" name="nombre" required placeholder="Nombre (ej. Pedir seña)"
                class="w-full border rounded-lg px-3 py-2 text-sm mb-2">
            <textarea name="texto" rows="3" required placeholder="Texto del mensaje…"
                class="w-full border rounded-lg px-3 py-2 text-sm font-mono mb-2"></textarea>
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Guardar mensaje</button>
        </form>
    </div>

    {{-- Listado --}}
    @forelse($guardados as $g)
    <div x-data="{ editar: false }" class="bg-white rounded-xl shadow p-5">
        <div x-show="!editar" class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="font-medium text-gray-800">{{ $g->nombre }} @unless($g->activo)<span class="text-xs text-gray-400">(inactivo)</span>@endunless</p>
                <p class="text-sm text-gray-500 whitespace-pre-line mt-1">{{ Str::limit($g->texto, 160) }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button @click="editar = true" class="text-blue-600 hover:underline text-sm">Editar</button>
                <form method="POST" action="{{ route('whatsapp.guardados.destroy', $g) }}" onsubmit="return confirm('¿Eliminar este mensaje?')">
                    @csrf @method('DELETE')
                    <button class="text-red-400 hover:text-red-600 text-sm">Eliminar</button>
                </form>
            </div>
        </div>
        <form x-show="editar" x-cloak method="POST" action="{{ route('whatsapp.guardados.update', $g) }}">
            @csrf @method('PUT')
            <input type="text" name="nombre" value="{{ $g->nombre }}" required class="w-full border rounded-lg px-3 py-2 text-sm mb-2">
            <textarea name="texto" rows="3" required class="w-full border rounded-lg px-3 py-2 text-sm font-mono mb-2">{{ $g->texto }}</textarea>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" {{ $g->activo ? 'checked' : '' }}> Activo
                </label>
                <div class="flex gap-2">
                    <button type="button" @click="editar = false" class="text-gray-500 text-sm px-2">Cancelar</button>
                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm">Guardar</button>
                </div>
            </div>
        </form>
    </div>
    @empty
    <p class="text-sm text-gray-400">Todavía no guardaste mensajes.</p>
    @endforelse
</div>
@endsection
