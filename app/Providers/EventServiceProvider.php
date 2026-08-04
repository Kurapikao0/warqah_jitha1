<?php

namespace App\Providers;

use App\Events\CustomerRegistered;
use App\Listeners\SendVerificationOtpListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
    protected $listen = [

        CustomerRegistered::class => [
            SendVerificationOtpListener::class,
        ],

    ];
}