<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar — TallerFácil</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 sm:p-6">

<div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-2">

    {{-- Panel izquierdo (branding) --}}
    <div class="relative hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800 text-white overflow-hidden">
        {{-- Textura sutil --}}
        <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-yellow-500/10 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-10 w-72 h-72 rounded-full bg-yellow-500/5 blur-3xl"></div>

        <div class="relative">
            <div class="flex items-center gap-2 mb-12">
                <span class="text-3xl">🔧</span>
                <span class="text-xl font-bold tracking-tight">TallerFácil</span>
            </div>

            <h2 class="text-3xl font-bold leading-tight mb-3">Todo tu taller,<br>en un solo lugar</h2>
            <p class="text-gray-400 text-sm leading-relaxed mb-8 max-w-xs">
                Gestioná órdenes, cotizaciones y clientes desde una sola pantalla, en la compu o el celular.
            </p>

            <ul class="space-y-3 text-sm">
                @foreach([
                    'Tablero de órdenes',
                    'Cotizaciones con PDF',
                    'Clientes y vehículos',
                    'Recordatorios por WhatsApp',
                    'Agenda y calendario',
                ] as $feature)
                <li class="flex items-center gap-3">
                    <span class="w-5 h-5 rounded-full bg-yellow-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3 text-yellow-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <span class="text-gray-200">{{ $feature }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        <p class="relative text-xs text-gray-500 mt-10">© {{ date('Y') }} TallerFácil — Sistema de gestión</p>
    </div>

    {{-- Panel derecho (formulario) --}}
    <div class="p-8 sm:p-10 flex flex-col justify-center">

        {{-- Logo compacto solo en móvil --}}
        <div class="lg:hidden flex items-center gap-2 mb-8">
            <span class="text-2xl">🔧</span>
            <span class="text-lg font-bold text-gray-900">TallerFácil</span>
        </div>

        <h1 class="text-2xl font-bold text-gray-900">Iniciar sesión</h1>
        <p class="text-gray-500 text-sm mb-6">Ingresá tus credenciales para acceder.</p>

        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" x-data="{ show: false }">
            @csrf

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" autofocus required
                        placeholder="tu@email.com"
                        class="w-full border rounded-lg pl-10 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('email') border-red-400 @enderror">
                </div>
            </div>

            {{-- Contraseña --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input :type="show ? 'text' : 'password'" name="password" required
                        placeholder="••••••••"
                        class="w-full border rounded-lg pl-10 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    <button type="button" @click="show = !show" tabindex="-1"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                        <svg x-show="!show" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-show="show" x-cloak class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                    </button>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-600 mb-6">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500"> Mantener sesión iniciada
            </label>

            <button type="submit"
                class="w-full flex items-center justify-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 rounded-lg text-sm transition-colors">
                Ingresar
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </form>

        @if(session('status'))
        <div class="mt-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('status') }}
        </div>
        @endif

        <div class="mt-5 text-center">
            <a href="{{ route('password.request') }}" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                ¿Olvidaste tu contraseña?
            </a>
        </div>
    </div>
</div>

</body>
</html>
