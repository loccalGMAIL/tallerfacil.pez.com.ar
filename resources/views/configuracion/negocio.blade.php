@extends('layouts.app')
@section('title', 'Configuración · Negocio')

@section('content')
@include('configuracion._nav')

<div class="max-w-2xl bg-white rounded-xl shadow p-5 sm:p-6">
    <p class="text-sm text-gray-500 mb-5">Estos datos aparecen en los PDF de cotización y en los mensajes al cliente.</p>

    <form method="POST" action="{{ route('configuracion.negocio.update') }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del taller <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $negocio->nombre) }}"
                    class="w-full border rounded-lg px-3 py-2.5 text-sm @error('nombre') border-red-400 @enderror">
                @error('nombre')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Razón social</label>
                <input type="text" name="razon_social" value="{{ old('razon_social', $negocio->razon_social) }}"
                    class="w-full border rounded-lg px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CUIT</label>
                <input type="text" name="cuit" value="{{ old('cuit', $negocio->cuit) }}"
                    class="w-full border rounded-lg px-3 py-2.5 text-sm" placeholder="30-12345678-9">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="telefono" value="{{ old('telefono', $negocio->telefono) }}"
                    class="w-full border rounded-lg px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $negocio->email) }}"
                    class="w-full border rounded-lg px-3 py-2.5 text-sm @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <input type="text" name="direccion" value="{{ old('direccion', $negocio->direccion) }}"
                    class="w-full border rounded-lg px-3 py-2.5 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Horario de atención</label>
                <input type="text" name="horario" value="{{ old('horario', $negocio->horario) }}"
                    class="w-full border rounded-lg px-3 py-2.5 text-sm" placeholder="Lun a Vie 8 a 18 hs">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <textarea name="notas" rows="2" class="w-full border rounded-lg px-3 py-2.5 text-sm">{{ old('notas', $negocio->notas) }}</textarea>
            </div>
        </div>

        <div class="flex">
            <button type="submit" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium">
                Guardar
            </button>
        </div>
    </form>
</div>
@endsection
