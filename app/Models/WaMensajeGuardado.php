<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaMensajeGuardado extends Model
{
    protected $table = 'wa_mensajes_guardados';

    protected $fillable = ['nombre', 'texto', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
