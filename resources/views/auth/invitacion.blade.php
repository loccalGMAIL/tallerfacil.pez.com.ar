<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta — TallerFácil</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 sm:p-6">

<div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-2">

    {{-- Panel izquierdo (branding) --}}
    <div class="relative hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800 text-white overflow-hidden">
        <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-yellow-500/10 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-10 w-72 h-72 rounded-full bg-yellow-500/5 blur-3xl"></div>

        <div class="relative">
            <div class="flex items-center gap-2 mb-12">
                <span class="text-3xl">🔧</span>
                <span class="text-xl font-bold tracking-tight">TallerFácil</span>
            </div>
            <h2 class="text-3xl font-bold leading-tight mb-3">Te invitaron<br>al equipo</h2>
            <p class="text-gray-400 text-sm leading-relaxed max-w-xs">
                Creá tu cuenta para acceder al sistema de gestión del taller.
            </p>
        </div>

        <p class="relative text-xs text-gray-500 mt-10">© {{ date('Y') }} TallerFácil — Sistema de gestión</p>
    </div>

    {{-- Panel derecho --}}
    <div class="p-8 sm:p-10 flex flex-col justify-center">

        <div class="lg:hidden flex items-center gap-2 mb-8">
            <span class="text-2xl">🔧</span>
            <span class="text-lg font-bold text-gray-900">TallerFácil</span>
        </div>

        @if($estado === 'usada')
            <div class="text-center py-6">
                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <h1 class="text-xl font-bold text-gray-800 mb-2">Invitación ya utilizada</h1>
                <p class="text-gray-500 text-sm">Esta invitación ya fue usada para crear una cuenta. Si necesitás acceso, contactá al administrador.</p>
                <a href="{{ route('login') }}" class="inline-block mt-6 text-sm text-yellow-600 hover:underline">Ir al login</a>
            </div>

        @elseif($estado === 'expirada')
            <div class="text-center py-6">
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h1 class="text-xl font-bold text-gray-800 mb-2">Invitación expirada</h1>
                <p class="text-gray-500 text-sm">El link de invitación venció. Pedile al administrador que genere uno nuevo.</p>
                <a href="{{ route('login') }}" class="inline-block mt-6 text-sm text-yellow-600 hover:underline">Ir al login</a>
            </div>

        @else
            <h1 class="text-2xl font-bold text-gray-900">Hola, {{ $invitacion->nombre }}</h1>
            <p class="text-gray-500 text-sm mb-6">Creá tu cuenta para acceder al sistema.</p>

            <div class="mb-5 p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg>
                {{ $invitacion->email }}
            </div>

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            {{-- Opción 1: Google --}}
            <a href="{{ route('auth.google', ['invitation' => $invitacion->token]) }}"
               class="w-full flex items-center justify-center gap-3 border border-gray-300 rounded-lg py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors mb-4">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continuar con Google
            </a>

            {{-- Separador --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-xs text-gray-400">o creá una contraseña</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            {{-- Opción 2: Contraseña --}}
            <form method="POST" action="{{ route('invitacion.register', $invitacion->token) }}" x-data="{ show: false }">
                @csrf

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" required
                            placeholder="Mínimo 8 caracteres"
                            class="w-full border rounded-lg px-3 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('password') border-red-400 @enderror">
                        <button type="button" @click="show = !show" tabindex="-1"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="show" x-cloak class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" required
                        placeholder="Repetí la contraseña"
                        class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                </div>

                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 rounded-lg text-sm transition-colors">
                    Crear cuenta con contraseña
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </form>
        @endif

    </div>
</div>

</body>
</html>
