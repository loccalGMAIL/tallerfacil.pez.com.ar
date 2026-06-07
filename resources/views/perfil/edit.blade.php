@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
<div class="max-w-lg mx-auto">

    <h1 class="text-xl font-bold text-gray-800 mb-6">Mi perfil</h1>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Cabecera con avatar y rol --}}
        <div class="bg-gray-900 px-6 py-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-yellow-400 flex items-center justify-center text-gray-900 font-bold text-xl shrink-0">
                {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
            </div>
            <div>
                <p class="text-white font-semibold leading-tight">{{ $usuario->nombre }}</p>
                <p class="text-gray-400 text-xs mt-0.5">{{ $usuario->email }}</p>
                <span class="inline-block mt-1.5 text-[10px] uppercase tracking-widest font-medium text-gray-500 border border-gray-700 rounded px-1.5 py-0.5">
                    {{ $usuario->rol }}
                </span>
            </div>
        </div>

        {{-- Formulario --}}
        <form method="POST" action="{{ route('perfil.update') }}" class="px-6 py-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent @error('nombre') border-red-400 @enderror">
                @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Email</label>
                <input type="email" name="email" value="{{ old('email', $usuario->email) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent @error('email') border-red-400 @enderror">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs font-medium text-gray-500 mb-4 uppercase tracking-wide">Cambiar contraseña <span class="normal-case text-gray-400 font-normal">(opcional)</span></p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">Nueva contraseña</label>
                        <input type="password" name="password"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent @error('password') border-red-400 @enderror"
                            placeholder="Mínimo 8 caracteres">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent @error('password_confirmation') border-red-400 @enderror"
                            placeholder="Repetí la contraseña">
                        @error('password_confirmation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit"
                    class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-semibold text-sm px-5 py-2 rounded-lg transition-colors">
                    Guardar cambios
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
