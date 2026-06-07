<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OrdenFoto extends Model
{
    protected $table = 'orden_fotos';

    protected $fillable = ['orden_id', 'ruta', 'tipo', 'descripcion'];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(Orden::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->ruta);
    }
}
