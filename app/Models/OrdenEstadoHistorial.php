<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenEstadoHistorial extends Model
{
    public $timestamps = false;

    protected $fillable = ['orden_id', 'estado', 'usuario_id', 'notas', 'fecha_hora'];

    protected function casts(): array
    {
        return ['fecha_hora' => 'datetime'];
    }

    public function orden(): BelongsTo
    {
        return $this->belongsTo(Orden::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}
