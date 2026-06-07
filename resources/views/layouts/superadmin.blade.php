<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') — TallerFácil Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-gray-950 min-h-screen text-gray-100" x-data>

<nav class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-14">
        <div class="flex items-center gap-6">
            <a href="{{ route('superadmin.dashboard') }}" class="font-bold text-lg tracking-tight text-yellow-400">
                ⚙️ TallerFácil Admin
            </a>
            <div class="hidden md:flex gap-1 text-sm">
                <a href="{{ route('superadmin.dashboard') }}"
                   class="px-3 py-1.5 rounded-md transition-colors hover:bg-gray-800 {{ request()->routeIs('superadmin.dashboard') ? 'text-yellow-400 bg-gray-800' : 'text-gray-300' }}">
                    Dashboard
                </a>
                <a href="{{ route('superadmin.talleres.index') }}"
                   class="px-3 py-1.5 rounded-md transition-colors hover:bg-gray-800 {{ request()->routeIs('superadmin.talleres.*') ? 'text-yellow-400 bg-gray-800' : 'text-gray-300' }}">
                    Talleres
                </a>
                <a href="{{ route('superadmin.usuarios.index') }}"
                   class="px-3 py-1.5 rounded-md transition-colors hover:bg-gray-800 {{ request()->routeIs('superadmin.usuarios.*') ? 'text-yellow-400 bg-gray-800' : 'text-gray-300' }}">
                    Usuarios
                </a>
                <a href="{{ route('superadmin.suscripciones.index') }}"
                   class="px-3 py-1.5 rounded-md transition-colors hover:bg-gray-800 {{ request()->routeIs('superadmin.suscripciones.*') ? 'text-yellow-400 bg-gray-800' : 'text-gray-300' }}">
                    Suscripciones
                </a>
                <a href="{{ route('superadmin.logs.index') }}"
                   class="px-3 py-1.5 rounded-md transition-colors hover:bg-gray-800 {{ request()->routeIs('superadmin.logs.*') ? 'text-yellow-400 bg-gray-800' : 'text-gray-300' }}">
                    Logs
                </a>
            </div>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <span class="text-gray-400">{{ auth()->user()->nombre }}</span>
            <form method="POST" action="{{ route('superadmin.logout') }}">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-white transition-colors">
                    Salir
                </button>
            </form>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 py-6">
    @if (session('success'))
        <div class="mb-4 bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('reset_link'))
        <div class="mb-4 bg-blue-900/50 border border-blue-700 text-blue-300 px-4 py-3 rounded-lg text-sm break-all">
            <strong>Link de reset:</strong> {{ session('reset_link') }}
        </div>
    @endif

    @yield('content')
</main>

</body>
</html>
