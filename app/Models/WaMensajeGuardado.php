<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTaller;
use Illuminate\Database\Eloquent\Model;

class WaMensajeGuardado extends Model
{
    use BelongsToTaller;

    protected $table = 'wa_mensajes_guardados';

    protected $fillable = ['nombre', 'texto', 'activo', 'taller_id'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
