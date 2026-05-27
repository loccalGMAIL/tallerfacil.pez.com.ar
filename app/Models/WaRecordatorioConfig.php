<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaRecordatorioConfig extends Model
{
    protected $table = 'wa_recordatorio_config';

    public $timestamps = false;

    protected $fillable = [
        'umbral_meses', 'umbral_km', 'ventana_minima_dias',
        'activo', 'tope_diario', 'delay_min_seg', 'delay_max_seg',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public static function instancia(): ?self
    {
        return self::find(1);
    }
}
