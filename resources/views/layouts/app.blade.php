<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TallerFácil') — TallerFácil</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-gray-100 min-h-screen" x-data>

<nav class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-14">
        <div class="flex items-center gap-6">
            <a href="{{ route('ordenes.index') }}" class="font-bold text-lg tracking-tight">🔧 TallerFácil</a>
            <div class="hidden md:flex gap-1 text-sm">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-colors hover:bg-gray-800 hover:text-yellow-400 {{ request()->routeIs('dashboard') ? 'text-yellow-400 bg-gray-800' : 'text-gray-300' }}">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: {{ request()->routeIs('dashboard') ? '#facc15' : '#a78bfa' }}">
                        <rect x="3" y="3" width="7" height="9" rx="1"/>
                        <rect x="14" y="3" width="7" height="5" rx="1"/>
                        <rect x="14" y="12" width="7" height="9" rx="1"/>
                        <rect x="3" y="16" width="7" height="5" rx="1"/>
                    </svg>
                    Panel
                </a>
                <a href="{{ route('ordenes.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-colors hover:bg-gray-800 hover:text-yellow-400 {{ request()->routeIs('ordenes.*') ? 'text-yellow-400 bg-gray-800' : 'text-gray-300' }}">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: {{ request()->routeIs('ordenes.*') ? '#facc15' : '#fb923c' }}">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                        <path d="M9 12h6M9 16h4"/>
                    </svg>
                    Órdenes
                </a>
                <a href="{{ route('clientes.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-colors hover:bg-gray-800 hover:text-yellow-400 {{ request()->routeIs('clientes.*') ? 'text-yellow-400 bg-gray-800' : 'text-gray-300' }}">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: {{ request()->routeIs('clientes.*') ? '#facc15' : '#60a5fa' }}">
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0-3-3.85"/>
                    </svg>
                    Clientes
                </a>
                <a href="{{ route('vehiculos.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-colors hover:bg-gray-800 hover:text-yellow-400 {{ request()->routeIs('vehiculos.*') ? 'text-yellow-400 bg-gray-800' : 'text-gray-300' }}">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: {{ request()->routeIs('vehiculos.*') ? '#facc15' : '#34d399' }}">
                        <path d="M5 17H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h11l4 4v4a2 2 0 0 1-2 2h-1"/>
                        <path d="M14 7H8L6 11h10l-2-4Z"/>
                        <circle cx="7.5" cy="17.5" r="2.5"/>
                        <circle cx="17.5" cy="17.5" r="2.5"/>
                    </svg>
                    Vehículos
                </a>
                @if(auth()->user()->esAdministrador())
                <a href="{{ route('whatsapp.mensajes') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-colors hover:bg-gray-800 hover:text-yellow-400 {{ request()->routeIs('whatsapp.*') ? 'text-yellow-400 bg-gray-800' : 'text-gray-300' }}">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: {{ request()->routeIs('whatsapp.*') ? '#facc15' : '#4ade80' }}">
                        <path fill="{{ request()->routeIs('whatsapp.*') ? '#facc15' : '#4ade80' }}" fill-opacity="0.15" stroke="{{ request()->routeIs('whatsapp.*') ? '#facc15' : '#4ade80' }}" d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z"/>
                    </svg>
                    WhatsApp
                </a>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('perfil.edit') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                <div class="w-7 h-7 rounded-full bg-yellow-400 flex items-center justify-center text-gray-900 font-bold text-xs shrink-0">
                    {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                </div>
                <span class="text-gray-300 hidden sm:inline">{{ auth()->user()->nombre }}</span>
                <span class="text-[10px] text-gray-500 uppercase tracking-widest hidden sm:inline">· {{ auth()->user()->rol }}</span>
            </a>
            @if(auth()->user()->esAdministrador())
            <a href="{{ route('configuracion.index') }}" title="Configuración"
               class="text-gray-400 hover:text-yellow-400 transition-colors {{ request()->routeIs('configuracion.*') ? 'text-yellow-400' : '' }}">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/>
                </svg>
            </a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-1 text-gray-500 hover:text-red-400 transition-colors text-xs">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Salir
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- Bottom nav mobile --}}
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-gray-900 border-t border-gray-700 flex items-stretch h-16">
    <a href="{{ route('dashboard') }}" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('dashboard') ? 'text-yellow-400 bg-gray-800' : 'text-gray-400' }}">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: {{ request()->routeIs('dashboard') ? '#facc15' : '#a78bfa' }}">
            <rect x="3" y="3" width="7" height="9" rx="1"/>
            <rect x="14" y="3" width="7" height="5" rx="1"/>
            <rect x="14" y="12" width="7" height="9" rx="1"/>
            <rect x="3" y="16" width="7" height="5" rx="1"/>
        </svg>
        <span class="text-[10px] font-medium leading-none">Panel</span>
    </a>
    <a href="{{ route('ordenes.index') }}" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('ordenes.*') ? 'text-yellow-400 bg-gray-800' : 'text-gray-400' }}">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: {{ request()->routeIs('ordenes.*') ? '#facc15' : '#fb923c' }}">
            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
            <rect x="9" y="3" width="6" height="4" rx="1"/>
            <path d="M9 12h6M9 16h4"/>
        </svg>
        <span class="text-[10px] font-medium leading-none">Órdenes</span>
    </a>
    <a href="{{ route('clientes.index') }}" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('clientes.*') ? 'text-yellow-400 bg-gray-800' : 'text-gray-400' }}">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: {{ request()->routeIs('clientes.*') ? '#facc15' : '#60a5fa' }}">
            <circle cx="9" cy="7" r="4"/>
            <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75M21 21v-2a4 4 0 0 0-3-3.85"/>
        </svg>
        <span class="text-[10px] font-medium leading-none">Clientes</span>
    </a>
    <a href="{{ route('vehiculos.index') }}" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('vehiculos.*') ? 'text-yellow-400 bg-gray-800' : 'text-gray-400' }}">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: {{ request()->routeIs('vehiculos.*') ? '#facc15' : '#34d399' }}">
            <path d="M5 17H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h11l4 4v4a2 2 0 0 1-2 2h-1"/>
            <path d="M14 7H8L6 11h10l-2-4Z"/>
            <circle cx="7.5" cy="17.5" r="2.5"/>
            <circle cx="17.5" cy="17.5" r="2.5"/>
        </svg>
        <span class="text-[10px] font-medium leading-none">Vehículos</span>
    </a>
    @if(auth()->user()->esAdministrador())
    <a href="{{ route('whatsapp.mensajes') }}" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors {{ request()->routeIs('whatsapp.*') ? 'text-yellow-400 bg-gray-800' : 'text-gray-400' }}">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: {{ request()->routeIs('whatsapp.*') ? '#facc15' : '#4ade80' }}">
            <path fill="{{ request()->routeIs('whatsapp.*') ? '#facc15' : '#4ade80' }}" fill-opacity="0.15" stroke="{{ request()->routeIs('whatsapp.*') ? '#facc15' : '#4ade80' }}" d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z"/>
        </svg>
        <span class="text-[10px] font-medium leading-none">WhatsApp</span>
    </a>
    @endif
</nav>

<main class="max-w-7xl mx-auto px-4 py-6 pb-20 md:pb-6">

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded text-sm">
        {{ session('error') }}
    </div>
    @endif

    @yield('content')
</main>

</body>
</html>
