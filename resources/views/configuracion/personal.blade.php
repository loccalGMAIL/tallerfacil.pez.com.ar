@extends('layouts.app')
@section('title', 'Configuración · Personal')

@section('content')
@include('configuracion._nav')

<div class="max-w-3xl space-y-5">

    {{-- Alta de usuario --}}
    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="font-semibold text-gray-800 mb-3">Agregar usuario</h2>
        <form method="POST" action="{{ route('configuracion.personal.store') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input type="text" name="nombre" required placeholder="Nombre"
                    class="border rounded-lg px-3 py-2.5 text-sm @error('nombre') border-red-400 @enderror">
                <input type="email" name="email" required placeholder="Email"
                    class="border rounded-lg px-3 py-2.5 text-sm @error('email') border-red-400 @enderror">
                <select name="rol" class="border rounded-lg px-3 py-2.5 text-sm">
                    <option value="mecanico">Mecánico</option>
                    <option value="administrador">Administrador</option>
                </select>
                <input type="text" name="password" required placeholder="Contraseña inicial (mín. 8)"
                    autocomplete="new-password"
                    class="border rounded-lg px-3 py-2.5 text-sm @error('password') border-red-400 @enderror">
            </div>
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            <div class="mt-3">
                <button type="submit" class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium">
                    + Crear usuario
                </button>
            </div>
        </form>
    </div>

    {{-- Listado --}}
    <div class="bg-white rounded-xl shadow divide-y divide-gray-100">
        @foreach($usuarios as $usuario)
        <div x-data="{ editar: false }" class="p-4">
            {{-- Vista --}}
            <div x-show="!editar" class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-yellow-400 flex items-center justify-center text-gray-900 font-bold text-sm shrink-0">
                        {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium text-gray-800 truncate">
                            {{ $usuario->nombre }}
                            @unless($usuario->activo)<span class="text-xs text-gray-400">(inactivo)</span>@endunless
                        </p>
                        <p class="text-xs text-gray-500 truncate">{{ $usuario->email }} · <span class="capitalize">{{ $usuario->rol }}</span></p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button @click="editar = true" class="text-blue-600 hover:underline text-sm">Editar</button>
                    @if($usuario->id !== auth()->id() && $usuario->activo)
                    <form method="POST" action="{{ route('configuracion.personal.destroy', $usuario) }}"
                        onsubmit="return confirm('¿Desactivar este usuario?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-600 text-sm">Desactivar</button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Edición --}}
            <form x-show="editar" x-cloak method="POST" action="{{ route('configuracion.personal.update', $usuario) }}">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <input type="text" name="nombre" value="{{ $usuario->nombre }}" required
                        class="border rounded-lg px-3 py-2 text-sm">
                    <input type="email" name="email" value="{{ $usuario->email }}" required
                        class="border rounded-lg px-3 py-2 text-sm">
                    <select name="rol" class="border rounded-lg px-3 py-2 text-sm">
                        <option value="mecanico" {{ $usuario->rol === 'mecanico' ? 'selected' : '' }}>Mecánico</option>
                        <option value="administrador" {{ $usuario->rol === 'administrador' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    <input type="text" name="password" placeholder="Nueva contraseña (opcional)" autocomplete="new-password"
                        class="border rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="flex items-center justify-between mt-2">
                    <label class="flex items-center gap-1.5 text-sm text-gray-600">
                        <input type="checkbox" name="activo" value="1" {{ $usuario->activo ? 'checked' : '' }}> Activo
                    </label>
                    <div class="flex gap-2">
                        <button type="button" @click="editar = false" class="text-gray-500 text-sm px-2">Cancelar</button>
                        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection
