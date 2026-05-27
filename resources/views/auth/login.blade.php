<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar — TallerFácil</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">

<div class="w-full max-w-sm bg-white rounded-xl shadow-lg p-8">
    <div class="text-center mb-6">
        <div class="text-4xl mb-2">🔧</div>
        <h1 class="text-xl font-bold text-gray-900">TallerFácil</h1>
        <p class="text-gray-500 text-sm">Sistema de gestión</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                autofocus
                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('email') border-red-400 @enderror"
                placeholder="admin@tallerfacil.com"
            >
            @error('email')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input
                type="password"
                name="password"
                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"
            >
        </div>

        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" class="rounded"> Recordarme
            </label>
        </div>

        <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 rounded-lg text-sm transition">
            Ingresar
        </button>
    </form>
</div>

</body>
</html>
