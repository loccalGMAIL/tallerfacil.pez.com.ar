<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PersonalController extends Controller
{
    public function index(): View
    {
        $usuarios = Usuario::orderBy('nombre')->get();

        return view('configuracion.personal', compact('usuarios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:usuarios,email'],
            'rol'      => ['required', 'in:administrador,mecanico'],
            'password' => ['required', Password::min(8)],
        ], $this->mensajes());

        Usuario::create([
            'nombre'   => $request->nombre,
            'email'    => $request->email,
            'rol'      => $request->rol,
            'activo'   => true,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Usuario creado.');
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:usuarios,email,' . $usuario->id],
            'rol'      => ['required', 'in:administrador,mecanico'],
            'activo'   => ['nullable', 'boolean'],
            'password' => ['nullable', Password::min(8)],
        ], $this->mensajes());

        $usuario->nombre = $request->nombre;
        $usuario->email  = $request->email;
        $usuario->rol    = $request->rol;
        $usuario->activo = $request->boolean('activo');
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }
        $usuario->save();

        return back()->with('success', 'Usuario actualizado.');
    }

    public function destroy(Usuario $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No podés desactivar tu propio usuario.');
        }

        $usuario->update(['activo' => false]);

        return back()->with('success', 'Usuario desactivado.');
    }

    private function mensajes(): array
    {
        return [
            'email.unique'   => 'Ese email ya está en uso.',
            'password.min'   => 'La contraseña debe tener al menos 8 caracteres.',
            'nombre.required' => 'El nombre es obligatorio.',
        ];
    }
}
