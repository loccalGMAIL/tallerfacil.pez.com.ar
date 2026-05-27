<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WaRecordatorioConfigSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('wa_recordatorio_config')->insertOrIgnore([
            'id'                  => 1,
            'umbral_meses'        => 6,
            'umbral_km'           => 10000,
            'ventana_minima_dias' => 30,
            'activo'              => false,
            'tope_diario'         => 50,
            'delay_min_seg'       => 30,
            'delay_max_seg'       => 90,
            'updated_at'          => now(),
        ]);
    }
}
