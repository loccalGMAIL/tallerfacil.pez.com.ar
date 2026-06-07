<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTaller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaPlantilla extends Model
{
    use BelongsToTaller;

    protected $table = 'wa_plantillas';

    protected $fillable = ['tipo', 'texto', 'activo', 'taller_id'];

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
