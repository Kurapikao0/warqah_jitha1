<?php

namespace App\Listeners;

use App\Events\CustomerRegistered;
use App\Notifications\WelcomeNotification;
use App\Services\NotificationService;
use App\Enums\EmailNotificationType;
class SendWelcomeEmailListener
{

    public function __construct(
        protected NotificationService $notificationService
    ){
    }
    public function handle(
        CustomerRegistered $event
    ): void {

        $this->notificationService->send(

            $event->customer,

            new WelcomeNotification(),

        );

    }

}