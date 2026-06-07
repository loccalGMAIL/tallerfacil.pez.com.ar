<?php

namespace App\Models;

use App\Mail\ResetPasswordMail;
use App\Models\Concerns\BelongsToTaller;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Usuario extends Authenticatable implements CanResetPasswordContract
{
    use Notifiable, BelongsToTaller, CanResetPassword, LogsActivity;

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

    public function sendPasswordResetNotification($token): void
    {
        $base = $this->taller_id
            ? 'https://' . optional($this->taller)->subdominio . '.' . config('app.base_domain')
            : config('app.url');

        $url = $base . '/reset-password/' . $token . '?email=' . urlencode($this->email);

        Mail::to($this->email)->send(new ResetPasswordMail($url));
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nombre', 'email', 'rol', 'activo'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function ordenes(): HasMany
    {
        return $this->hasMany(Orden::class, 'mecanico_id');
    }
}
