@extends('layouts.superadmin')

@section('title', 'Talleres')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Talleres</h1>
    <a href="{{ route('superadmin.talleres.create') }}"
       class="bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold px-4 py-2 rounded-lg text-sm transition-colors">
        + Nuevo taller
    </a>
</div>

<form method="GET" class="mb-4">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Buscar por nombre o subdominio…"
           class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white w-72 focus:outline-none focus:border-yellow-500">
</form>

<div class="bg-gray-800 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-900 text-gray-400">
            <tr>
                <th class="text-left px-4 py-3">Taller</th>
                <th class="text-left px-4 py-3">Subdominio</th>
                <th class="text-left px-4 py-3">Usuarios</th>
                <th class="text-left px-4 py-3">Suscripción</th>
                <th class="text-left px-4 py-3">Estado</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
            @forelse($talleres as $taller)
            <tr class="hover:bg-gray-750 transition-colors">
                <td class="px-4 py-3">
                    <a href="{{ route('superadmin.talleres.show', $taller) }}"
                       class="text-blue-400 hover:underline font-medium">{{ $taller->nombre }}</a>
                </td>
                <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $taller->subdominio }}</td>
                <td class="px-4 py-3 text-gray-300">{{ $taller->usuarios_count }}</td>
                <td class="px-4 py-3">
                    @if($taller->suscripcionActual)
                        <span class="px-2 py-0.5 rounded text-xs
                            {{ $taller->suscripcionActual->estado === 'activo' ? 'bg-green-900 text-green-300' : '' }}
                            {{ $taller->suscripcionActual->estado === 'prueba' ? 'bg-blue-900 text-blue-300' : '' }}
                            {{ $taller->suscripcionActual->estado === 'vencido' ? 'bg-red-900 text-red-300' : '' }}
                            {{ $taller->suscripcionActual->estado === 'cancelado' ? 'bg-gray-700 text-gray-400' : '' }}
                        ">
                            {{ ucfirst($taller->suscripcionActual->estado) }}
                        </span>
                    @else
                        <span class="text-gray-600 text-xs">Sin suscripción</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="text-xs {{ $taller->activo ? 'text-green-400' : 'text-red-400' }}">
                        {{ $taller->activo ? '● Activo' : '● Inactivo' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('superadmin.talleres.show', $taller) }}"
                       class="text-gray-400 hover:text-white text-xs">Ver →</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">No hay talleres registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $talleres->links() }}</div>
@endsection
