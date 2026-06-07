@php
    $tabs = [
        ['route' => 'whatsapp.config',       'label' => 'Conexión',     'match' => 'whatsapp.config'],
        ['route' => 'whatsapp.plantillas',   'label' => 'Plantillas',   'match' => 'whatsapp.plantillas'],
        ['route' => 'whatsapp.recordatorio', 'label' => 'Configuración', 'match' => 'whatsapp.recordatorio'],
        ['route' => 'whatsapp.mensajes',     'label' => 'Mensajes',     'match' => 'whatsapp.mensajes'],
    ];
@endphp

<div class="flex items-center gap-2 mb-6">
    <svg class="w-6 h-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z"/>
    </svg>
    <h1 class="text-xl font-bold text-gray-900">WhatsApp</h1>
</div>

<div class="border-b border-gray-200 mb-6 overflow-x-auto overflow-y-hidden">
    <nav class="flex gap-1 -mb-px whitespace-nowrap">
        @foreach($tabs as $tab)
        @php $activa = request()->routeIs($tab['match']); @endphp
        <a href="{{ route($tab['route']) }}"
           class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
                  {{ $activa ? 'border-green-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            {{ $tab['label'] }}
        </a>
        @endforeach
    </nav>
</div>
