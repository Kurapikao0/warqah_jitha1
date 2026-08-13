<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Customer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class CustomerRegistered
{
    use Dispatchable;
    use SerializesModels;


    public function __construct(
        public readonly Customer $customer
    ) {
    }
}
