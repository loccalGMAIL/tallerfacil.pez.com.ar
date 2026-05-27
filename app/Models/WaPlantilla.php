<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaPlantilla extends Model
{
    protected $table = 'wa_plantillas';

    protected $fillable = ['tipo', 'texto', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(WaMensaje::class, 'plantilla_id');
    }

    public static function activa(string $tipo): ?self
    {
        return self::where('tipo', $tipo)->where('activo', true)->first();
    }
}
