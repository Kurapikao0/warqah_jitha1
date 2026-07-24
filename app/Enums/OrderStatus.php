<?php

namespace App\Enums;

/**
 * Shared between the `orders.status` and `order_status_history.status`
 * columns, which are defined with an identical enum value set in the
 * migrations. Centralising them here removes the duplicated definition
 * from the application layer (the duplication still exists as two
 * separate CHECK constraints at the database level, since migrations
 * are the source of truth and were not altered).
 */
enum OrderStatus: string
{
    case Received = 'received';
    case InProduction = 'in_production';
    case InTransit = 'in_transit';
    case Cancelled = 'cancelled';
}
