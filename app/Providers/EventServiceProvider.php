<?php

namespace App\Providers;

use App\Events\CustomerRegistered;
use App\Listeners\SendVerificationOtpListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\PasswordResetRequested;
use App\Listeners\SendPasswordResetLinkListener;

class EventServiceProvider extends ServiceProvider
{

    
  //  
    
}