@php use App\Models\Orden; @endphp
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4">

    {{-- Navegación de mes --}}
    <div class="flex items-center justify-between mb-3">
        <a href="{{ route('dashboard', ['vista' => 'calendario', 'mes' => $cal['prev']]) }}"
           class="px-3 py-1.5 rounded-lg hover:bg-gray-100 text-gray-600">←</a>
        <h2 class="font-semibold text-gray-800">{{ $cal['mes_label'] }}</h2>
        <a href="{{ route('dashboard', ['vista' => 'calendario', 'mes' => $cal['next']]) }}"
           class="px-3 py-1.5 rounded-lg hover:bg-gray-100 text-gray-600">→</a>
    </div>

    {{-- Cabecera de días --}}
    <div class="grid grid-cols-7 gap-1 mb-1 text-center text-[10px] sm:text-xs font-medium text-gray-400 uppercase">
        @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $d)
        <div>{{ $d }}</div>
        @endforeach
    </div>

    {{-- Grilla --}}
    <div class="grid grid-cols-7 gap-1">
        @foreach($cal['dias'] as $dia)
        <div class="min-h-[64px] sm:min-h-[92px] rounded-lg border p-1
                    {{ $dia['delMes'] ? 'bg-white border-gray-100' : 'bg-gray-50 border-gray-50' }}
                    {{ $dia['esHoy'] ? 'ring-2 ring-yellow-400' : '' }}">
            <div class="text-[10px] sm:text-xs mb-0.5 {{ $dia['delMes'] ? 'text-gray-500' : 'text-gray-300' }} {{ $dia['esHoy'] ? 'font-bold text-yellow-600' : '' }}">
                {{ $dia['fecha']->day }}
            </div>
            <div class="space-y-0.5">
                @foreach($dia['ordenes']->take(3) as $orden)
                <a href="{{ route('ordenes.show', $orden) }}"
                   class="block text-[9px] sm:text-[11px] leading-tight truncate rounded px-1 py-0.5 {{ $orden->estadoBadge() }}"
                   title="{{ $orden->vehiculo->patente }} — {{ Orden::ESTADO_LABELS[$orden->estado] ?? $orden->estado }}">
                    {{ $orden->vehiculo->patente }}
                </a>
                @endforeach
                @if($dia['ordenes']->count() > 3)
                <span class="block text-[9px] text-gray-400 px-1">+{{ $dia['ordenes']->count() - 3 }} más</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
