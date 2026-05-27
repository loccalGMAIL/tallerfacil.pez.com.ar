<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WaConfigSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('wa_config')->insertOrIgnore([
            'id'              => 1,
            'url_base'        => '',
            'api_key'         => '',
            'instancia'       => '',
            'webhook_secret'  => bin2hex(random_bytes(16)),
            'estado_conexion' => 'desconocido',
            'updated_at'      => now(),
        ]);
    }
}
