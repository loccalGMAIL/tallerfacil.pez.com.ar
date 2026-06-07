@extends('layouts.app')
@section('title', 'Nueva Orden')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('ordenes.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Órdenes</a>
    <h1 class="text-xl font-bold text-gray-900">Nueva orden de trabajo</h1>
</div>

<div class="max-w-2xl bg-white rounded-xl shadow p-6">
    <form method="POST" action="{{ route('ordenes.store') }}">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Vehículo <span class="text-red-500">*</span></label>
                <select name="vehiculo_id" class="w-full border rounded-lg px-3 py-2 text-sm @error('vehiculo_id') border-red-400 @enderror">
                    <option value="">— Seleccionar vehículo —</option>
                    @foreach($vehiculos as $v)
                    <option value="{{ $v->id }}" {{ (old('vehiculo_id', $vehiculo?->id) == $v->id) ? 'selected' : '' }}>
                        {{ $v->patente }} — {{ $v->marca }} {{ $v->modelo }} ({{ $v->cliente->nombre }})
                    </option>
                    @endforeach
                </select>
                @error('vehiculo_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de ingreso</label>
                <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', now()->format('Y-m-d')) }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Km de ingreso</label>
                <input type="number" name="km_ingreso" value="{{ old('km_ingreso') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="85000">
            </div>

            @if(auth()->user()->esAdministrador())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mecánico asignado</label>
                <select name="mecanico_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">— Sin asignar —</option>
                    @foreach($mecanicos as $m)
                    <option value="{{ $m->id }}" {{ old('mecanico_id') == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción del problema</label>
                <textarea name="descripcion" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm"
                    placeholder="Síntoma reportado por el cliente...">{{ old('descripcion') }}</textarea>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3">
            <button type="submit" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium">
                Crear orden
            </button>
            <a href="{{ route('ordenes.index') }}" class="w-full sm:w-auto text-center text-gray-600 hover:text-gray-800 px-4 py-2.5 text-sm">Cancelar</a>
        </div>
    </form>
</div>
@endsection
