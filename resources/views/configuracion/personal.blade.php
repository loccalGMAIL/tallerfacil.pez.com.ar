@extends('layouts.app')
@section('title', 'Configuración · Personal')

@section('content')
@include('configuracion._nav')

<div class="max-w-3xl space-y-5">

    @if(session('invitacion_link'))
    <div x-data="{ copiado: false }" class="bg-green-50 border border-green-200 rounded-xl p-4">
        <p class="text-sm font-medium text-green-800 mb-2">
            Invitación generada para <strong>{{ session('invitacion_nombre') }}</strong>. Compartí este link:
        </p>
        <div class="flex gap-2">
            <input type="text" readonly value="{{ session('invitacion_link') }}"
                class="flex-1 border border-green-300 bg-white rounded-lg px-3 py-2 text-xs text-gray-700 focus:outline-none"
                @click="$el.select()">
            <button @click="navigator.clipboard.writeText('{{ session('invitacion_link') }}'); copiado = true; setTimeout(() => copiado = false, 2000)"
                class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg font-medium transition-colors shrink-0">
                <span x-show="!copiado">Copiar</span>
                <span x-show="copiado" x-cloak>¡Copiado!</span>
            </button>
        </div>
        <p class="text-xs text-green-600 mt-1.5">El link expira en 7 días.</p>
    </div>
    @endif

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800">
        {{ session('success') }}
    </div>
    @endif

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

    {{-- Invitar mecánico --}}
    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="font-semibold text-gray-800 mb-1">Invitar por link</h2>
        <p class="text-xs text-gray-500 mb-3">Generá un link de registro para que el usuario cree su propia cuenta con contraseña o con Google.</p>
        <form method="POST" action="{{ route('configuracion.personal.invitacion.store') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input type="text" name="nombre" required placeholder="Nombre"
                    class="border rounded-lg px-3 py-2.5 text-sm @error('nombre') border-red-400 @enderror">
                <input type="email" name="email" required placeholder="Email"
                    class="border rounded-lg px-3 py-2.5 text-sm @error('email') border-red-400 @enderror">
                <select name="rol" class="border rounded-lg px-3 py-2.5 text-sm sm:col-span-2">
                    <option value="mecanico">Mecánico</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            <div class="mt-3">
                <button type="submit" class="w-full sm:w-auto bg-gray-800 hover:bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium">
                    Generar link de invitación
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
                        <p class="text-xs text-gray-500 truncate flex items-center gap-1.5">
                            {{ $usuario->email }} · <span class="capitalize">{{ $usuario->rol }}</span>
                            @if($usuario->tieneGoogle())
                            <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-600 border border-blue-200 rounded px-1.5 py-0.5 text-[10px] font-medium">
                                <svg class="w-3 h-3" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                Google
                            </span>
                            @endif
                        </p>
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
