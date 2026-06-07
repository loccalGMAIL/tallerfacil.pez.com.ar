<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenTarea extends Model
{
    protected $table = 'orden_tareas';

    protected $fillable = ['orden_id', 'descripcion', 'posicion', 'completada'];

    protected function casts(): array
    {
        return [
            'completada' => 'boolean',
        ];
    }

    public function orden(): BelongsTo
    {
        return $this->belongsTo(Orden::class);
    }
}
