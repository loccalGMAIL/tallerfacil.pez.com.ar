@extends('layouts.superadmin')
@section('title', 'Usuarios')
@section('content')
<h1 class="text-2xl font-bold mb-6">Usuarios</h1>

<form method="GET" class="flex flex-wrap gap-3 mb-4">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o email…"
           class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white w-56 focus:outline-none focus:border-yellow-500">
    <select name="taller_id" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
        <option value="">Todos los talleres</option>
        @foreach($talleres as $t)
        <option value="{{ $t->id }}" {{ request('taller_id') == $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
        @endforeach
    </select>
    <select name="rol" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
        <option value="">Todos los roles</option>
        <option value="administrador" {{ request('rol') === 'administrador' ? 'selected' : '' }}>Administrador</option>
        <option value="mecanico" {{ request('rol') === 'mecanico' ? 'selected' : '' }}>Mecánico</option>
    </select>
    <button type="submit" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg text-sm transition-colors">Filtrar</button>
</form>

<div class="bg-gray-800 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-900 text-gray-400">
            <tr>
                <th class="text-left px-4 py-3">Nombre</th>
                <th class="text-left px-4 py-3">Email</th>
                <th class="text-left px-4 py-3">Taller</th>
                <th class="text-left px-4 py-3">Rol</th>
                <th class="text-left px-4 py-3">Estado</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
            @forelse($usuarios as $usuario)
            <tr>
                <td class="px-4 py-3">{{ $usuario->nombre }}</td>
                <td class="px-4 py-3 text-gray-400">{{ $usuario->email }}</td>
                <td class="px-4 py-3 text-gray-400">{{ $usuario->taller?->nombre ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-400">{{ $usuario->rol }}</td>
                <td class="px-4 py-3">
                    <span class="{{ $usuario->activo ? 'text-green-400' : 'text-red-400' }} text-xs">
                        {{ $usuario->activo ? '● Activo' : '● Inactivo' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('superadmin.usuarios.edit', $usuario) }}"
                       class="text-gray-400 hover:text-white text-xs mr-2">Editar</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">No hay usuarios.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $usuarios->links() }}</div>
@endsection
