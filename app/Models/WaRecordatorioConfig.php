<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaRecordatorioConfig extends Model
{
    protected $table = 'wa_recordatorio_config';

    public $timestamps = false;

    protected $fillable = [
        'umbral_meses', 'ventana_minima_dias',
        'activo', 'tope_diario', 'delay_min_seg', 'delay_max_seg',
        'auto_recepcion', 'auto_reparacion', 'auto_listo', 'auto_entregado',
    ];

    /** Estados cuyo cambio puede disparar un mensaje automático (estado => columna config). */
    public const AUTO_ESTADOS = [
        'recepcion'  => 'auto_recepcion',
        'reparacion' => 'auto_reparacion',
        'listo'      => 'auto_listo',
        'entregado'  => 'auto_entregado',
    ];

    protected function casts(): array
    {
        return [
            'activo'          => 'boolean',
            'auto_recepcion'  => 'boolean',
            'auto_reparacion' => 'boolean',
            'auto_listo'      => 'boolean',
            'auto_entregado'  => 'boolean',
        ];
    }

    public static function instancia(): ?self
    {
        return self::find(1);
    }
}
