<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTaller;
use Illuminate\Database\Eloquent\Model;

class WaConfig extends Model
{
    use BelongsToTaller;

    protected $table = 'wa_config';

    public $timestamps = false;

    protected $fillable = [
        'url_base', 'api_key', 'instancia',
        'webhook_secret', 'estado_conexion', 'taller_id',
    ];

    protected $hidden = ['api_key', 'webhook_secret'];

    // El Global Scope de BelongsToTaller ya filtra por taller_id automáticamente
    public static function instancia(): ?self
    {
        return self::first();
    }

    public function getApiKeyMaskedAttribute(): string
    {
        return substr($this->api_key, 0, 4) . str_repeat('*', 12);
    }
}
