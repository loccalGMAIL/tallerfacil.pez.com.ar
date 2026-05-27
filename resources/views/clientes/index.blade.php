@extends('layouts.app')
@section('title', 'Clientes')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-gray-900">Clientes</h1>
    @if(auth()->user()->esAdministrador())
    <a href="{{ route('clientes.create') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
        + Nuevo cliente
    </a>
    @endif
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Buscar por nombre, DNI o teléfono..."
        class="border rounded-lg px-3 py-2 text-sm w-72 focus:outline-none focus:ring-2 focus:ring-yellow-500"
    >
    <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Buscar</button>
    @if(request('search'))
    <a href="{{ route('clientes.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm">Limpiar</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Nombre</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Documento</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Teléfono</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Vehículos</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($clientes as $cliente)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <a href="{{ route('clientes.show', $cliente) }}" class="font-medium text-gray-900 hover:text-yellow-600">
                        {{ $cliente->nombre }}
                    </a>
                    @unless($cliente->activo)
                    <span class="ml-1 text-xs text-gray-400">(inactivo)</span>
                    @endunless
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $cliente->tipo_doc }}: {{ $cliente->nro_doc ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $cliente->telefono_display }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $cliente->email ?? '—' }}</td>
                <td class="px-4 py-3 text-center">{{ $cliente->vehiculos_count ?? $cliente->vehiculos()->count() }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('clientes.show', $cliente) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                    @if(auth()->user()->esAdministrador())
                    <a href="{{ route('clientes.edit', $cliente) }}" class="ml-2 text-gray-500 hover:underline text-xs">Editar</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Sin resultados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $clientes->links() }}</div>
@endsection
