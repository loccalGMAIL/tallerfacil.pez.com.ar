<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TallerFácil') — TallerFácil</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen" x-data>

<nav class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-14">
        <div class="flex items-center gap-6">
            <a href="{{ route('ordenes.index') }}" class="font-bold text-lg tracking-tight">🔧 TallerFácil</a>
            <div class="hidden md:flex gap-4 text-sm">
                <a href="{{ route('ordenes.index') }}" class="hover:text-yellow-400 {{ request()->routeIs('ordenes.*') ? 'text-yellow-400' : '' }}">Órdenes</a>
                <a href="{{ route('clientes.index') }}" class="hover:text-yellow-400 {{ request()->routeIs('clientes.*') ? 'text-yellow-400' : '' }}">Clientes</a>
                <a href="{{ route('vehiculos.index') }}" class="hover:text-yellow-400 {{ request()->routeIs('vehiculos.*') ? 'text-yellow-400' : '' }}">Vehículos</a>
                @if(auth()->user()->esAdministrador())
                <a href="{{ route('whatsapp.mensajes') }}" class="hover:text-yellow-400 {{ request()->routeIs('whatsapp.*') ? 'text-yellow-400' : '' }}">WhatsApp</a>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <span class="text-gray-400">{{ auth()->user()->nombre }}</span>
            <span class="text-xs px-2 py-0.5 rounded {{ auth()->user()->rol === 'administrador' ? 'bg-yellow-600' : 'bg-gray-600' }}">
                {{ auth()->user()->rol }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-white text-xs">Salir</button>
            </form>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 py-6">

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
