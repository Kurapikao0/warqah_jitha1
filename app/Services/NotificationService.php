<?php
namespace App\Services;

use App\Models\EmailNotification;
use App\Jobs\SendEmailNotificationJob;
use Illuminate\Notifications\Notification;
use App\Models\Customer;

class NotificationService
{

    public function send(
        Customer $customer,
        Notification $notification,
        string $type,
        string $subject,
        ?string $recipient = null
    ): void {


        $recipient = $recipient ?? $customer->email;


        $log = EmailNotification::create([

            'customer_id' => $customer->id,

            'type' => $type,

            'recipient' => $recipient,

            'subject' => $subject,

            'status' => 'pending',

            'attempts' => 0,

        ]);



        SendEmailNotificationJob::dispatch(
            $customer,
            $notification,
            $log,
            $recipient
        )
        ->delay(now()->addSeconds(10));


    }

}
