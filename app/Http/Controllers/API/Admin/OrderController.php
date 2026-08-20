<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service
    ) {}

    /**
     * Display all orders
     */
    public function index(\Illuminate\Http\Request $request)
    {

        return OrderResource::collection(

            $this->service->all((int) $request->query('per_page', 20))

        );

    }

    /**
     * Display order details
     */
    public function show(Order $order)
    {

        return new OrderResource(

            $this->service->find($order->id)

        );

    }

    /**
     * Delete order
     */
    public function destroy(Order $order)
    {
        $this->service->delete($order);

        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }

    public function statistics(\Illuminate\Http\Request $request)
    {
        return response()->json(
            $this->service->statistics(
                $request->query('from'),
                $request->query('to')
            )
        );
    }
}
