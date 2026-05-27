<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insertOrIgnore([
            'nombre'     => 'Administrador',
            'email'      => 'admin@tallerfacil.com',
            'password'   => Hash::make('password'),
            'rol'        => 'administrador',
            'activo'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
