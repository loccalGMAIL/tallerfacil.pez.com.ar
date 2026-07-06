<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Taller extends Model
{
    protected $fillable = [
        'nombre',
        'razon_social',
        'cuit',
        'telefono',
        'email',
        'direccion',
        'horario',
        'notas',
        'logo',
        'subdominio',
        'activo',
        'en_prueba',
    ];

    protected function casts(): array
    {
        return [
            'activo'    => 'boolean',
            'en_prueba' => 'boolean',
        ];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class)->withoutGlobalScopes();
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class)->withoutGlobalScopes();
    }

    public function suscripcionActual(): HasOne
    {
        return $this->hasOne(Suscripcion::class)->latestOfMany();
    }

    public function suscripciones(): HasMany
    {
        return $this->hasMany(Suscripcion::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function tieneSuscripcionActiva(): bool
    {
        return $this->suscripcionActual?->estaActiva() ?? false;
    }
}
