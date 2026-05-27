<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orden extends Model
{
    protected $table = 'ordenes';

    protected $fillable = [
        'numero', 'vehiculo_id', 'mecanico_id', 'fecha_ingreso',
        'km_ingreso', 'descripcion', 'estado', 'total_estimado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ingreso'   => 'date',
            'total_estimado'  => 'decimal:2',
        ];
    }

    // Transiciones de estado válidas
    public const TRANSICIONES = [
        'presupuesto' => ['aprobada', 'cancelada'],
        'aprobada'    => ['en_proceso', 'cancelada'],
        'en_proceso'  => ['finalizada', 'cancelada'],
        'finalizada'  => ['entregada', 'cancelada'],
        'entregada'   => [],
        'cancelada'   => [],
    ];

    public const ESTADOS_CERRADOS = ['entregada', 'cancelada'];

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function mecanico(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'mecanico_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrdenItem::class);
    }

    public function historial(): HasMany
    {
        return $this->hasMany(OrdenEstadoHistorial::class)->orderBy('fecha_hora', 'asc');
    }

    public function waMensajes(): HasMany
    {
        return $this->hasMany(WaMensaje::class);
    }

    public function estaAbierta(): bool
    {
        return !in_array($this->estado, self::ESTADOS_CERRADOS);
    }

    public function puedeTransicionarA(string $nuevoEstado): bool
    {
        return in_array($nuevoEstado, self::TRANSICIONES[$this->estado] ?? []);
    }

    public function recalcularTotal(): void
    {
        $this->total_estimado = $this->items()->sum('subtotal');
        $this->save();
    }

    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }
}
