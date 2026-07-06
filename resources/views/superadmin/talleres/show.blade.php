@extends('layouts.superadmin')

@section('title', $taller->nombre)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('superadmin.talleres.index') }}" class="text-gray-500 hover:text-gray-300 text-sm">← Talleres</a>
        <h1 class="text-2xl font-bold mt-1">{{ $taller->nombre }}</h1>
        <p class="text-gray-400 text-sm font-mono">{{ $taller->subdominio }}.{{ config('app.base_domain') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('superadmin.talleres.edit', $taller) }}"
           class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg text-sm transition-colors">
            Editar
        </a>
        @if($taller->activo)
        <form method="POST" action="{{ route('superadmin.talleres.desactivar', $taller) }}">
            @csrf
            <button type="submit" class="bg-red-900 hover:bg-red-800 text-red-300 px-4 py-2 rounded-lg text-sm transition-colors">
                Desactivar
            </button>
        </form>
        @else
        <form method="POST" action="{{ route('superadmin.talleres.activar', $taller) }}">
            @csrf
            <button type="submit" class="bg-green-900 hover:bg-green-800 text-green-300 px-4 py-2 rounded-lg text-sm transition-colors">
                Activar
            </button>
        </form>
        @endif
    </div>
</div>

<div class="grid md:grid-cols-3 gap-6 mb-6">
    <div class="bg-gray-800 rounded-xl p-5 md:col-span-2">
        <h2 class="font-semibold mb-3">Datos del taller</h2>
        <dl class="grid grid-cols-2 gap-3 text-sm">
            <div><dt class="text-gray-400">CUIT</dt><dd>{{ $taller->cuit ?: '—' }}</dd></div>
            <div><dt class="text-gray-400">Razón social</dt><dd>{{ $taller->razon_social ?: '—' }}</dd></div>
            <div><dt class="text-gray-400">Email</dt><dd>{{ $taller->email ?: '—' }}</dd></div>
            <div><dt class="text-gray-400">Teléfono</dt><dd>{{ $taller->telefono ?: '—' }}</dd></div>
            <div><dt class="text-gray-400">Dirección</dt><dd>{{ $taller->direccion ?: '—' }}</dd></div>
            <div><dt class="text-gray-400">En prueba</dt><dd>{{ $taller->en_prueba ? 'Sí' : 'No' }}</dd></div>
        </dl>
    </div>
    <div class="bg-gray-800 rounded-xl p-5">
        <h2 class="font-semibold mb-3">Suscripción</h2>
        @php $sus = $taller->suscripciones->first() @endphp
        @if($sus)
        <div class="text-2xl font-bold {{ $sus->estaActiva() ? 'text-green-400' : 'text-red-400' }} mb-1">
            {{ ucfirst($sus->estado) }}
        </div>
        <div class="text-sm text-gray-400">Plan: {{ ucfirst($sus->plan) }}</div>
        @if($sus->fecha_vencimiento)
        <div class="text-sm text-gray-400">Vence: {{ $sus->fecha_vencimiento->format('d/m/Y') }}</div>
        @endif
        @else
        <p class="text-gray-500 text-sm">Sin suscripción</p>
        @endif
        <a href="{{ route('superadmin.suscripciones.show', $taller) }}"
           class="mt-3 inline-block text-xs text-blue-400 hover:underline">
            Gestionar suscripción →
        </a>
    </div>
</div>

<div class="bg-gray-800 rounded-xl p-5">
    <h2 class="font-semibold mb-3">Usuarios ({{ $taller->usuarios->count() }})</h2>
    @if($taller->usuarios->isEmpty())
        <p class="text-gray-500 text-sm">Sin usuarios.</p>
    @else
    <table class="w-full text-sm">
        <thead class="text-gray-400">
            <tr>
                <th class="text-left pb-2">Nombre</th>
                <th class="text-left pb-2">Email</th>
                <th class="text-left pb-2">Rol</th>
                <th class="text-left pb-2">Estado</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
            @foreach($taller->usuarios as $usuario)
            <tr>
                <td class="py-2">{{ $usuario->nombre }}</td>
                <td class="py-2 text-gray-400">{{ $usuario->email }}</td>
                <td class="py-2 text-gray-400">{{ $usuario->rol }}</td>
                <td class="py-2">
                    <span class="{{ $usuario->activo ? 'text-green-400' : 'text-red-400' }}">
                        {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
