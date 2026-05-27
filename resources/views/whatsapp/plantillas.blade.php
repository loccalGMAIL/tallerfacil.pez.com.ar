@extends('layouts.app')
@section('title', 'Plantillas WhatsApp')

@section('content')
<h1 class="text-xl font-bold text-gray-900 mb-6">Plantillas de Mensaje</h1>
<p class="text-sm text-gray-500 mb-6">Variables disponibles: <code class="bg-gray-100 px-1 rounded">{nombre}</code> <code class="bg-gray-100 px-1 rounded">{marca}</code> <code class="bg-gray-100 px-1 rounded">{modelo}</code> <code class="bg-gray-100 px-1 rounded">{patente}</code> <code class="bg-gray-100 px-1 rounded">{total}</code> <code class="bg-gray-100 px-1 rounded">{numero_orden}</code> <code class="bg-gray-100 px-1 rounded">{fecha_ingreso}</code> <code class="bg-gray-100 px-1 rounded">{items_lista}</code></p>

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
@endsection
