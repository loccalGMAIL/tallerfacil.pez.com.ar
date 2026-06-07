<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTaller;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable, BelongsToTaller;

    protected $table = 'usuarios';

    protected $fillable = ['nombre', 'email', 'password', 'rol', 'activo', 'taller_id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo'   => 'boolean',
        ];
    }

    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }

    public function esSuperAdmin(): bool
    {
        return $this->rol === 'superadmin';
    }

    public function ordenes(): HasMany
    {
        return $this->hasMany(Orden::class, 'mecanico_id');
    }
}
