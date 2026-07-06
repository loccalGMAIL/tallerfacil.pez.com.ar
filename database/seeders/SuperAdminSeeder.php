<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::withoutGlobalScopes()->updateOrCreate(
            ['email' => env('SUPERADMIN_EMAIL', 'superadmin@tallerfacil.com.ar')],
            [
                'nombre'    => 'Super Admin',
                'password'  => Hash::make(env('SUPERADMIN_PASSWORD', 'changeme123!')),
                'rol'       => 'superadmin',
                'activo'    => true,
                'taller_id' => null,
            ]
        );
    }
}
