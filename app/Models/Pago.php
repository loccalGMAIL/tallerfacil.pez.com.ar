<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'suscripcion_id',
        'taller_id',
        'monto',
        'moneda',
        'estado',
        'mp_payment_id',
        'fecha_pago',
        'detalle_json',
    ];

    protected function casts(): array
    {
        return [
            'monto'       => 'decimal:2',
            'fecha_pago'  => 'datetime',
            'detalle_json' => 'array',
        ];
    }

    public function suscripcion(): BelongsTo
    {
        return $this->belongsTo(Suscripcion::class);
    }

    public function taller(): BelongsTo
    {
        return $this->belongsTo(Taller::class);
    }
}
