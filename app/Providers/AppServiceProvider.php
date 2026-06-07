<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event) {
            activity('auth')
                ->causedBy($event->user)
                ->log('login');
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                activity('auth')
                    ->causedBy($event->user)
                    ->log('logout');
            }
        });
    }
}

