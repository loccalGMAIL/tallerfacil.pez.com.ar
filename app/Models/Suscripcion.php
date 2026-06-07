<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Suscripcion extends Model
{
    protected $fillable = [
        'taller_id',
        'plan',
        'estado',
        'fecha_inicio',
        'fecha_vencimiento',
        'mp_preapproval_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio'      => 'datetime',
            'fecha_vencimiento' => 'datetime',
        ];
    }

    public function taller(): BelongsTo
    {
        return $this->belongsTo(Taller::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function estaActiva(): bool
    {
        if (!in_array($this->estado, ['activo', 'prueba'])) {
            return false;
        }

        if ($this->fecha_vencimiento !== null && $this->fecha_vencimiento->isPast()) {
            return false;
        }

        return true;
    }
}
