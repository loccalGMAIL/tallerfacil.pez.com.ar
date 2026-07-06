@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
<div class="max-w-lg mx-auto">

    <h1 class="text-xl font-bold text-gray-800 mb-6">Mi perfil</h1>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->has('google'))
    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        {{ $errors->first('google') }}
    </div>
    @endif

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

    {{-- Cuenta de Google --}}
    <div class="mt-5 bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-4">Cuenta de Google</p>

        @if($usuario->tieneGoogle())
        <div class="flex items-center gap-3">
            @if($usuario->google_avatar)
            <img src="{{ $usuario->google_avatar }}" alt="Avatar Google" class="w-9 h-9 rounded-full border border-gray-200">
            @else
            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
            </div>
            @endif
            <div>
                <p class="text-sm font-medium text-gray-800">Google vinculado</p>
                <p class="text-xs text-gray-500">Podés ingresar con tu cuenta de Google.</p>
            </div>
            <span class="ml-auto inline-flex items-center gap-1 text-xs text-green-600 bg-green-50 border border-green-200 rounded px-2 py-1">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Activo
            </span>
        </div>
        @else
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-gray-700">Google no vinculado</p>
                <p class="text-xs text-gray-500">Vincular te permite ingresar con un click.</p>
            </div>
            <a href="{{ route('auth.google') }}"
               class="inline-flex items-center gap-2 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors shrink-0">
                <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Vincular con Google
            </a>
        </div>
        @endif
    </div>

</div>
@endsection
