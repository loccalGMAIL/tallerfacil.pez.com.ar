@extends('layouts.app')
@section('title', 'Editar Vehículo')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('vehiculos.show', $vehiculo) }}" class="text-gray-500 hover:text-gray-700 text-sm">← {{ $vehiculo->patente }}</a>
    <h1 class="text-xl font-bold text-gray-900">Editar vehículo</h1>
</div>

<div class="max-w-2xl bg-white rounded-xl shadow p-6">
    <form method="POST" action="{{ route('vehiculos.update', $vehiculo) }}">
        @csrf @method('PUT')
        <input type="hidden" name="cliente_id" value="{{ $vehiculo->cliente_id }}">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Patente <span class="text-red-500">*</span></label>
                <input type="text" name="patente" value="{{ old('patente', $vehiculo->patente) }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm @error('patente') border-red-400 @enderror"
                    style="text-transform:uppercase">
                @error('patente')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Combustible</label>
                <select name="combustible" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">—</option>
                    @foreach(['nafta','diesel','gnc','electrico','hibrido','otro'] as $c)
                    <option value="{{ $c }}" {{ old('combustible', $vehiculo->combustible) === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marca <span class="text-red-500">*</span></label>
                <input type="text" name="marca" value="{{ old('marca', $vehiculo->marca) }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modelo <span class="text-red-500">*</span></label>
                <input type="text" name="modelo" value="{{ old('modelo', $vehiculo->modelo) }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                <input type="number" name="anio" value="{{ old('anio', $vehiculo->anio) }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Km actuales</label>
                <input type="number" name="km_actual" value="{{ old('km_actual', $vehiculo->km_actual) }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha último service</label>
                <input type="date" name="fecha_ultimo_service" value="{{ old('fecha_ultimo_service', $vehiculo->fecha_ultimo_service?->format('Y-m-d')) }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Km del último service</label>
                <input type="number" name="km_ultimo_service" value="{{ old('km_ultimo_service', $vehiculo->km_ultimo_service) }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <textarea name="notas" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('notas', $vehiculo->notas) }}</textarea>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3">
            <button type="submit" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium">Guardar</button>
            <a href="{{ route('vehiculos.show', $vehiculo) }}" class="w-full sm:w-auto text-center text-gray-600 hover:text-gray-800 px-4 py-2.5 text-sm">Cancelar</a>
        </div>
    </form>
</div>
@endsection
