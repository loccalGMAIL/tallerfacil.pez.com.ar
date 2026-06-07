<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva contraseña — TallerFácil</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
    <div class="flex items-center gap-2 mb-6">
        <span class="text-2xl">🔧</span>
        <span class="text-lg font-bold text-gray-900">TallerFácil</span>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 mb-1">Nueva contraseña</h1>
    <p class="text-gray-500 text-sm mb-6">Elegí una contraseña segura para tu cuenta.</p>

    @if($errors->any())
    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" x-data="{ show: false }">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required readonly
                class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2.5 text-sm text-gray-600">
        </div>

        <div class="mb-4" x-data="{ show: false }">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nueva contraseña</label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password" required minlength="8"
                    placeholder="Mínimo 8 caracteres"
                    class="w-full border rounded-lg pl-3 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('password') border-red-400 @enderror">
                <button type="button" @click="show = !show" tabindex="-1"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                    <svg x-show="!show" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                </button>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" required
                placeholder="Repetí la contraseña"
                class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
        </div>

        <button type="submit"
            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 rounded-lg text-sm transition-colors">
            Guardar nueva contraseña
        </button>
    </form>
</div>

</body>
</html>
