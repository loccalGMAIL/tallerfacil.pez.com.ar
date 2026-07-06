@extends('layouts.superadmin')

@section('title', 'Nuevo Taller')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Nuevo Taller</h1>

    <form method="POST" action="{{ route('superadmin.talleres.store') }}" class="space-y-6">
        @csrf

        <div class="bg-gray-800 rounded-xl p-5 space-y-4">
            <h2 class="font-semibold text-gray-300">Datos del taller</h2>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-yellow-500 @error('nombre') border-red-500 @enderror">
                @error('nombre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Subdominio *</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="subdominio" value="{{ old('subdominio') }}" required
                           placeholder="mitaller"
                           class="w-48 bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white font-mono text-sm focus:outline-none focus:border-yellow-500 @error('subdominio') border-red-500 @enderror">
                    <span class="text-gray-500 text-sm">.{{ config('app.base_domain') }}</span>
                </div>
                @error('subdominio') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-yellow-500">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-yellow-500">
                </div>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl p-5 space-y-4">
            <h2 class="font-semibold text-gray-300">Usuario administrador inicial</h2>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Nombre *</label>
                <input type="text" name="admin_nombre" value="{{ old('admin_nombre') }}" required
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-yellow-500 @error('admin_nombre') border-red-500 @enderror">
                @error('admin_nombre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Email *</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-yellow-500 @error('admin_email') border-red-500 @enderror">
                    @error('admin_email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Contraseña *</label>
                    <input type="password" name="admin_password" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-yellow-500 @error('admin_password') border-red-500 @enderror">
                    @error('admin_password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold px-5 py-2 rounded-lg transition-colors">
                Crear taller
            </button>
            <a href="{{ route('superadmin.talleres.index') }}"
               class="px-5 py-2 text-gray-400 hover:text-white transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
