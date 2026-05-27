<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehiculo extends Model
{
    protected $fillable = [
        'cliente_id', 'patente', 'marca', 'modelo', 'anio',
        'km_actual', 'combustible', 'fecha_ultimo_service',
        'km_ultimo_service', 'notas', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo'                => 'boolean',
            'fecha_ultimo_service'  => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ordenes(): HasMany
    {
        return $this->hasMany(Orden::class);
    }

    public function waMensajes(): HasMany
    {
        return $this->hasMany(WaMensaje::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->marca} {$this->modelo}" . ($this->anio ? " ({$this->anio})" : '');
    }
}
