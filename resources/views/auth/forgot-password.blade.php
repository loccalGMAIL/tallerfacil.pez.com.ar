<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña — TallerFácil</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
    <div class="flex items-center gap-2 mb-6">
        <span class="text-2xl">🔧</span>
        <span class="text-lg font-bold text-gray-900">TallerFácil</span>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 mb-1">Recuperar contraseña</h1>
    <p class="text-gray-500 text-sm mb-6">Te enviamos un link al email registrado.</p>

    @if(session('status'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
        {{ session('status') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                placeholder="tu@email.com"
                class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent @error('email') border-red-400 @enderror">
        </div>

        <button type="submit"
            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 rounded-lg text-sm transition-colors">
            Enviar link de recuperación
        </button>
    </form>

    <div class="mt-5 text-center">
        <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver al inicio de sesión</a>
    </div>
</div>

</body>
</html>
