@extends('layouts.app')
@section('title', 'Nuevo Vehículo')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ $cliente ? route('clientes.show', $cliente) : route('vehiculos.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">←</a>
    <h1 class="text-xl font-bold text-gray-900">Nuevo vehículo</h1>
    @if($cliente) <span class="text-gray-500 text-sm">para {{ $cliente->nombre }}</span> @endif
</div>

<div class="max-w-2xl bg-white rounded-xl shadow p-6">
    <form method="POST" action="{{ route('vehiculos.store') }}">
        @csrf

        <div class="grid grid-cols-2 gap-4 mb-4">
            @if(!$cliente)
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente <span class="text-red-500">*</span></label>
                <select name="cliente_id" class="w-full border rounded-lg px-3 py-2 text-sm @error('cliente_id') border-red-400 @enderror">
                    <option value="">— Seleccionar cliente —</option>
                    @foreach($clientes as $c)
                    <option value="{{ $c->id }}" {{ old('cliente_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->nombre }} ({{ $c->telefono_display }})
                    </option>
                    @endforeach
                </select>
                @error('cliente_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            @else
            <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Patente <span class="text-red-500">*</span></label>
                <input type="text" name="patente" value="{{ old('patente') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm @error('patente') border-red-400 @enderror"
                    placeholder="AB123CD" style="text-transform:uppercase">
                @error('patente')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Combustible</label>
                <select name="combustible" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">— Seleccionar —</option>
                    @foreach(['nafta','diesel','gnc','electrico','hibrido','otro'] as $c)
                    <option value="{{ $c }}" {{ old('combustible') === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marca <span class="text-red-500">*</span></label>
                <input type="text" name="marca" value="{{ old('marca') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm @error('marca') border-red-400 @enderror"
                    placeholder="Ford">
                @error('marca')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modelo <span class="text-red-500">*</span></label>
                <input type="text" name="modelo" value="{{ old('modelo') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm @error('modelo') border-red-400 @enderror"
                    placeholder="Focus">
                @error('modelo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                <input type="number" name="anio" value="{{ old('anio') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm"
                    placeholder="{{ now()->year }}" min="1900" max="{{ now()->year + 1 }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Km actuales</label>
                <input type="number" name="km_actual" value="{{ old('km_actual') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="85000">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha último service</label>
                <input type="date" name="fecha_ultimo_service" value="{{ old('fecha_ultimo_service') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Km del último service</label>
                <input type="number" name="km_ultimo_service" value="{{ old('km_ultimo_service') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="75000">
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <textarea name="notas" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm"
                    placeholder="Observaciones...">{{ old('notas') }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg text-sm font-medium">
                Registrar vehículo
            </button>
            <a href="{{ $cliente ? route('clientes.show', $cliente) : route('vehiculos.index') }}" class="text-gray-600 px-4 py-2 text-sm">Cancelar</a>
        </div>
    </form>
</div>
@endsection
