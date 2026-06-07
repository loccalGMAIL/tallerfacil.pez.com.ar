@extends('layouts.app')
@section('title', 'Nuevo Cliente')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('clientes.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Clientes</a>
    <h1 class="text-xl font-bold text-gray-900">Nuevo cliente</h1>
</div>

<div class="max-w-2xl bg-white rounded-xl shadow p-6">
    <form method="POST" action="{{ route('clientes.store') }}">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre / Razón social <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm @error('nombre') border-red-400 @enderror">
                @error('nombre')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de documento</label>
                <select name="tipo_doc" class="w-full border rounded-lg px-3 py-2 text-sm">
                    @foreach(['DNI','CUIT','CUIL'] as $tipo)
                    <option value="{{ $tipo }}" {{ old('tipo_doc', 'DNI') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de documento</label>
                <input type="text" name="nro_doc" value="{{ old('nro_doc') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm @error('nro_doc') border-red-400 @enderror"
                    placeholder="30123456">
                @error('nro_doc')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono <span class="text-red-500">*</span></label>
                <input type="text" name="telefono_display" value="{{ old('telefono_display') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm @error('telefono_display') border-red-400 @enderror"
                    placeholder="011 4444-5555">
                @error('telefono_display')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-gray-400 text-xs mt-1">Formato libre — se normaliza automáticamente.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm @error('email') border-red-400 @enderror"
                    placeholder="cliente@email.com">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion') }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm"
                    placeholder="Av. Corrientes 1234, CABA">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <textarea name="notas" rows="3"
                    class="w-full border rounded-lg px-3 py-2 text-sm"
                    placeholder="Observaciones opcionales...">{{ old('notas') }}</textarea>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3">
            <button type="submit" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium">
                Guardar cliente
            </button>
            <a href="{{ route('clientes.index') }}" class="w-full sm:w-auto text-center text-gray-600 hover:text-gray-800 px-4 py-2.5 text-sm">Cancelar</a>
        </div>
    </form>
</div>
@endsection
