@extends('layouts.superadmin')
@section('title', 'Log de eventos')
@section('content')
<h1 class="text-2xl font-bold mb-6">Log de eventos</h1>

<form method="GET" class="flex flex-wrap gap-3 mb-4">
    <select name="taller_id" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
        <option value="">Todos los talleres</option>
        @foreach($talleres as $t)
        <option value="{{ $t->id }}" {{ request('taller_id') == $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
        @endforeach
    </select>
    <input type="text" name="log_name" value="{{ request('log_name') }}" placeholder="Categoría (auth, default…)"
           class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white w-44 focus:outline-none focus:border-yellow-500">
    <input type="date" name="desde" value="{{ request('desde') }}"
           class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
    <input type="date" name="hasta" value="{{ request('hasta') }}"
           class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
    <button type="submit" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg text-sm transition-colors">Filtrar</button>
</form>

<div class="bg-gray-800 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-900 text-gray-400">
            <tr>
                <th class="text-left px-4 py-3">Fecha</th>
                <th class="text-left px-4 py-3">Categoría</th>
                <th class="text-left px-4 py-3">Evento</th>
                <th class="text-left px-4 py-3">Usuario</th>
                <th class="text-left px-4 py-3">Sujeto</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
            @forelse($logs as $log)
            <tr>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3">
                    <span class="bg-gray-700 text-gray-300 text-xs px-2 py-0.5 rounded">{{ $log->log_name }}</span>
                </td>
                <td class="px-4 py-3 text-gray-200">{{ $log->description }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $log->causer?->nombre ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    @if($log->subject_type)
                        {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                    @else
                        —
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Sin registros.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
