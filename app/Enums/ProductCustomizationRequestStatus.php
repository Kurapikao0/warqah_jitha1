<?php

namespace App\Enums;

enum ProductCustomizationRequestStatus: string
{
    case PendingApproval = 'pending_approval';
    case InProduction = 'in_production';
    case Completed = 'completed';
}
