<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;

class OrderStatusController extends Controller
{
    public function __construct(
        protected OrderService $service
    ) {}

    public function update(
        UpdateOrderStatusRequest $request,
        Order $order
    ) {

        $this->service
            ->updateStatus(
                $order,
                $request->validated()
            );

        return response()->json([

            'message' => 'Order status updated',

        ]);

    }
}
