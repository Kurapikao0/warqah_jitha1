<?php

namespace App\Http\Controllers\API\Admin;


use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;



class OrderController extends Controller
{


    public function __construct(
        protected OrderService $service
    )
    {

    }




    /**
     * Display all orders
     */
    public function index()
    {

        return OrderResource::collection(

            $this->service->all()

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
            'message'=>'Order deleted successfully'
        ]);
    }

    public function statistics()
{
    return response()->json(
        $this->service->statistics()
    );
}


}