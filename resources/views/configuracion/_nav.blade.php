@php
    $tabs = [
        ['route' => 'configuracion.negocio',   'label' => 'Negocio',   'match' => 'configuracion.negocio'],
        ['route' => 'configuracion.servicios', 'label' => 'Servicios', 'match' => 'configuracion.servicios'],
        ['route' => 'configuracion.personal',  'label' => 'Personal',  'match' => 'configuracion.personal'],
        ['route' => 'whatsapp.config',         'label' => 'WhatsApp',  'match' => 'whatsapp.*'],
    ];
@endphp

<div class="flex items-center gap-2 mb-6">
    <svg class="w-6 h-6 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="3"/>
        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/>
    </svg>
    <h1 class="text-xl font-bold text-gray-900">Configuración</h1>
</div>

<div class="border-b border-gray-200 mb-6 overflow-x-auto overflow-y-hidden">
    <nav class="flex gap-1 -mb-px whitespace-nowrap">
        @foreach($tabs as $tab)
        @php $activa = request()->routeIs($tab['match']); @endphp
        <a href="{{ route($tab['route']) }}"
           class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
                  {{ $activa ? 'border-yellow-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            {{ $tab['label'] }}
        </a>
        @endforeach
    </nav>
</div>
