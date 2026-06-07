@extends('layouts.superadmin')
@section('title', 'Editar ' . $taller->nombre)
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('superadmin.talleres.show', $taller) }}" class="text-gray-500 hover:text-gray-300 text-sm">← {{ $taller->nombre }}</a>
    <h1 class="text-2xl font-bold mt-1 mb-6">Editar taller</h1>

    <form method="POST" action="{{ route('superadmin.talleres.update', $taller) }}" class="space-y-4">
        @csrf @method('PUT')

        <div class="bg-gray-800 rounded-xl p-5 space-y-4">
            @foreach([
                ['nombre','Nombre','text',true],
                ['subdominio','Subdominio','text',true],
                ['razon_social','Razón social','text',false],
                ['cuit','CUIT','text',false],
                ['email','Email','email',false],
                ['telefono','Teléfono','text',false],
                ['direccion','Dirección','text',false],
            ] as [$field,$label,$type,$required])
            <div>
                <label class="block text-sm text-gray-400 mb-1">{{ $label }}{{ $required ? ' *' : '' }}</label>
                <input type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $taller->$field) }}" {{ $required ? 'required' : '' }}
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-yellow-500">
                @error($field) <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            @endforeach
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold px-5 py-2 rounded-lg transition-colors">
                Guardar
            </button>
            <a href="{{ route('superadmin.talleres.show', $taller) }}" class="px-5 py-2 text-gray-400 hover:text-white transition-colors">Cancelar</a>
        </div>
    </form>
</div>
@endsection
