<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaConfig extends Model
{
    protected $table = 'wa_config';

    public $timestamps = false;

    protected $fillable = [
        'url_base', 'api_key', 'instancia',
        'webhook_secret', 'estado_conexion',
    ];

    protected $hidden = ['api_key', 'webhook_secret'];

    public static function instancia(): ?self
    {
        return self::find(1);
    }

    public function getApiKeyMaskedAttribute(): string
    {
        return substr($this->api_key, 0, 4) . str_repeat('*', 12);
    }
}
