<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegocioConfig extends Model
{
    protected $table = 'negocio_config';

    protected $fillable = [
        'nombre', 'razon_social', 'cuit', 'telefono', 'email', 'direccion', 'horario', 'notas',
    ];

    public static function instancia(): self
    {
        return static::firstOrCreate(['id' => 1], ['nombre' => 'TallerFácil']);
    }
}
