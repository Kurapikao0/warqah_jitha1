<?php

namespace App\Enums;

enum CustomerNotificationType: string
{
    case OrderUpdate = 'order_update';
    case Promotion = 'promotion';
    case System = 'system';
}
