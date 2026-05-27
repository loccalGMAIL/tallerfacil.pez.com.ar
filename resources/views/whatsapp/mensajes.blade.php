@extends('layouts.app')
@section('title', 'Log de Mensajes WA')

@section('content')
<h1 class="text-xl font-bold text-gray-900 mb-6">Mensajes WhatsApp</h1>

<form method="GET" class="mb-4 flex gap-2">
    <select name="tipo" class="border rounded-lg px-3 py-2 text-sm">
        <option value="">Todos los tipos</option>
        <option value="presupuesto" {{ request('tipo') === 'presupuesto' ? 'selected' : '' }}>Presupuesto</option>
        <option value="recepcion" {{ request('tipo') === 'recepcion' ? 'selected' : '' }}>Recepción</option>
        <option value="recordatorio" {{ request('tipo') === 'recordatorio' ? 'selected' : '' }}>Recordatorio</option>
    </select>
    <select name="estado_entrega" class="border rounded-lg px-3 py-2 text-sm">
        <option value="">Todos los estados</option>
        @foreach(['pendiente','enviado','entregado','leido','fallido'] as $e)
        <option value="{{ $e }}" {{ request('estado_entrega') === $e ? 'selected' : '' }}>{{ ucfirst($e) }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Filtrar</button>
    @if(request()->hasAny(['tipo','estado_entrega']))
    <a href="{{ route('whatsapp.mensajes') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm">Limpiar</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Fecha</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Cliente</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Tipo</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Origen</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Estado</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Mensaje</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($mensajes as $msg)
            <tr>
                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $msg->fecha_hora->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3">
                    @if($msg->cliente)
                    <a href="{{ route('clientes.show', $msg->cliente) }}" class="hover:underline">{{ $msg->cliente->nombre }}</a>
                    @else —
                    @endif
                </td>
                <td class="px-4 py-3 capitalize">{{ $msg->tipo }}</td>
                <td class="px-4 py-3">
                    <span class="px-1.5 py-0.5 rounded text-xs {{ $msg->origen === 'n8n' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $msg->origen }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="px-1.5 py-0.5 rounded text-xs {{ match($msg->estado_entrega) {
                        'leido' => 'bg-blue-100 text-blue-700',
                        'entregado' => 'bg-green-100 text-green-700',
                        'fallido' => 'bg-red-100 text-red-700',
                        'enviado' => 'bg-yellow-100 text-yellow-700',
                        default => 'bg-gray-100 text-gray-500'
                    } }}">{{ $msg->estado_entrega }}</span>
                    @if($msg->error_detalle)
                    <span class="text-red-400 text-xs ml-1" title="{{ $msg->error_detalle }}">⚠</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ Str::limit($msg->contenido, 80) }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Sin mensajes.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $mensajes->links() }}</div>
@endsection
