<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use App\Listeners\LogAdminLogin;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
       Login::class => [
            LogAdminLogin::class,
        ],
    ];


    public function boot(): void
    {
        //
    }
}
