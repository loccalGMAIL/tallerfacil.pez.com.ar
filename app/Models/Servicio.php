<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTaller;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use BelongsToTaller;

    protected $table = 'servicios';

    protected $fillable = ['nombre', 'descripcion', 'tipo', 'precio', 'activo'];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
