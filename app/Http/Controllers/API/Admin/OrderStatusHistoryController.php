<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderStatusHistoryResource;
use App\Models\Order;
use App\Services\OrderStatusHistoryService;

class OrderStatusHistoryController extends Controller
{
    public function __construct(
        protected OrderStatusHistoryService $service
    ) {}

    public function index(Order $order)
    {

        return OrderStatusHistoryResource::collection(

            $this->service
                ->orderHistory($order->id)

        );

    }
}
