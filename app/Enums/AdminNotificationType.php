<?php

namespace App\Enums;

enum AdminNotificationType: string
{
    case NewOrder = 'new_order';
    case NewCustomizationRequest = 'new_customization_request';
    case System = 'system';
}
