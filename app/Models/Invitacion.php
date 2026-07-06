<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTaller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitacion extends Model
{
    use BelongsToTaller;

    protected $table = 'invitaciones';

    protected $fillable = ['token', 'email', 'nombre', 'rol', 'expires_at', 'used_at', 'created_by', 'taller_id'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at'    => 'datetime',
        ];
    }

    public function estaVigente(): bool
    {
        return is_null($this->used_at) && $this->expires_at->isFuture();
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }
}
