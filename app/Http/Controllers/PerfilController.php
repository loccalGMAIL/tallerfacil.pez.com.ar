<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function edit()
    {
        return view('perfil.edit', ['usuario' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $usuario = auth()->user();

        $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:usuarios,email,' . $usuario->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'nombre.required'    => 'El nombre es obligatorio.',
            'email.required'     => 'El email es obligatorio.',
            'email.unique'       => 'Ese email ya está en uso.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $usuario->nombre = $request->nombre;
        $usuario->email  = $request->email;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->route('perfil.edit')->with('success', 'Perfil actualizado correctamente.');
    }

    public function desvincularGoogle(Request $request)
    {
        $usuario = auth()->user();

        if (is_null($usuario->password)) {
            return redirect()->route('perfil.edit')
                ->withErrors(['google' => 'Antes de desvincular Google, configurá una contraseña para no quedarte sin acceso.']);
        }

        $usuario->update(['google_id' => null, 'google_avatar' => null]);

        return redirect()->route('perfil.edit')->with('success', 'Cuenta de Google desvinculada correctamente.');
    }
}
