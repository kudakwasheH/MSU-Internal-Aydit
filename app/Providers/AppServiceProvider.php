<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use App\Listeners\LogAuthenticationEvents;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, LogAuthenticationEvents::class);
        Event::listen(Logout::class, LogAuthenticationEvents::class);
        Event::listen(Failed::class, LogAuthenticationEvents::class);
        Event::listen(Lockout::class, LogAuthenticationEvents::class);
    }
}
