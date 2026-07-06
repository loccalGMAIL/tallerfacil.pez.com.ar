@extends('layouts.superadmin')
@section('title', 'Editar usuario')
@section('content')
<div class="max-w-xl">
    <a href="{{ route('superadmin.usuarios.index') }}" class="text-gray-500 hover:text-gray-300 text-sm">← Usuarios</a>
    <h1 class="text-2xl font-bold mt-1 mb-6">{{ $usuario->nombre }}</h1>

    <form method="POST" action="{{ route('superadmin.usuarios.update', $usuario) }}" class="space-y-4">
        @csrf @method('PUT')
        <div class="bg-gray-800 rounded-xl p-5 space-y-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-yellow-500">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-yellow-500">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Rol *</label>
                <select name="rol" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-yellow-500">
                    <option value="administrador" {{ old('rol', $usuario->rol) === 'administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="mecanico" {{ old('rol', $usuario->rol) === 'mecanico' ? 'selected' : '' }}>Mecánico</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', $usuario->activo) ? 'checked' : '' }}
                       class="rounded">
                <label for="activo" class="text-sm text-gray-300">Activo</label>
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold px-5 py-2 rounded-lg transition-colors">Guardar</button>
            <a href="{{ route('superadmin.usuarios.index') }}" class="px-5 py-2 text-gray-400 hover:text-white transition-colors">Cancelar</a>
        </div>
    </form>

    <div class="mt-6 bg-gray-800 rounded-xl p-5">
        <h2 class="font-semibold mb-3">Reset de contraseña</h2>
        <p class="text-sm text-gray-400 mb-3">Genera un link de reset para que el usuario pueda cambiar su contraseña.</p>
        <form method="POST" action="{{ route('superadmin.usuarios.reset-password', $usuario) }}">
            @csrf
            <button type="submit" class="bg-blue-800 hover:bg-blue-700 text-blue-200 px-4 py-2 rounded-lg text-sm transition-colors">
                Generar link de reset
            </button>
        </form>
    </div>
</div>
@endsection
