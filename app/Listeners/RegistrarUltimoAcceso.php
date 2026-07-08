<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

// Actualiza último acceso e IP en cada login (lo consume el portal admin).
class RegistrarUltimoAcceso
{
    public function handle(Login $event): void
    {
        $event->user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ])->saveQuietly();
    }
}
