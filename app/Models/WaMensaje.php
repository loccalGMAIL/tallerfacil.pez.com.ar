<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaMensaje extends Model
{
    protected $table = 'wa_mensajes';

    protected $fillable = [
        'cliente_id', 'orden_id', 'vehiculo_id', 'tipo', 'contenido',
        'origen', 'evolution_message_id', 'estado_entrega', 'error_detalle',
        'intentos', 'fecha_hora', 'fecha_entregado', 'fecha_leido',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora'      => 'datetime',
            'fecha_entregado' => 'datetime',
            'fecha_leido'     => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function orden(): BelongsTo
    {
        return $this->belongsTo(Orden::class);
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado_entrega', 'pendiente');
    }
}
