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
        'recepcion'  => ['cotizacion', 'cancelado'],
        'cotizacion' => ['reparacion', 'cancelado'],
        'reparacion' => ['listo', 'cancelado'],
        'listo'      => ['entregado', 'cancelado'],
        'entregado'  => [],
        'cancelado'  => [],
    ];

    public const ESTADOS_CERRADOS = ['entregado', 'cancelado'];

    // Estados que se muestran como columnas del tablero Kanban (en orden)
    public const ESTADOS_TABLERO = ['recepcion', 'cotizacion', 'reparacion', 'listo'];

    // Etiquetas legibles por estado
    public const ESTADO_LABELS = [
        'recepcion'  => 'Recepción',
        'cotizacion' => 'Cotización',
        'reparacion' => 'Reparación',
        'listo'      => 'Listo',
        'entregado'  => 'Entregado',
        'cancelado'  => 'Cancelado',
    ];

    // Clases de badge por estado
    public const ESTADO_BADGES = [
        'recepcion'  => 'bg-blue-100 text-blue-700',
        'cotizacion' => 'bg-purple-100 text-purple-700',
        'reparacion' => 'bg-yellow-100 text-yellow-800',
        'listo'      => 'bg-green-100 text-green-700',
        'entregado'  => 'bg-green-700 text-white',
        'cancelado'  => 'bg-red-100 text-red-700',
    ];

    public function estadoLabel(): string
    {
        return self::ESTADO_LABELS[$this->estado] ?? ucfirst($this->estado);
    }

    public function estadoBadge(): string
    {
        return self::ESTADO_BADGES[$this->estado] ?? 'bg-gray-100 text-gray-700';
    }

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

    public function tareas(): HasMany
    {
        return $this->hasMany(OrdenTarea::class)->orderBy('posicion');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(OrdenFoto::class)->latest();
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
