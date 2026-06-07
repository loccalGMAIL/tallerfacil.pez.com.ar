<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Cliente extends Model
{
    protected $fillable = [
        'nombre', 'tipo_doc', 'nro_doc', 'telefono_normalizado',
        'telefono_display', 'email', 'direccion', 'notas', 'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class);
    }

    public function ordenes(): HasManyThrough
    {
        return $this->hasManyThrough(Orden::class, Vehiculo::class);
    }

    public function waMensajes(): HasMany
    {
        return $this->hasMany(WaMensaje::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeBuscar($query, string $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('nro_doc', 'like', "%{$termino}%")
              ->orWhere('telefono_normalizado', 'like', "%{$termino}%")
              ->orWhere('telefono_display', 'like', "%{$termino}%");
        });
    }

    public function tieneOrdenesAbiertas(): bool
    {
        return $this->ordenes()
            ->whereNotIn('estado', ['entregado', 'cancelado'])
            ->exists();
    }
}
