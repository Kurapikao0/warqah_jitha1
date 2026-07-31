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


        $order->delete();



        return response()->json([

            'message'=>'Order deleted successfully'

        ]);


    }





    /**
     * Dashboard statistics
     */
    public function statistics()
    {


        return response()->json([


            'total_orders'=>
                Order::count(),


            'pending'=>
                Order::where(
                    'status',
                    'received'
                )->count(),



            'production'=>
                Order::where(
                    'status',
                    'in_production'
                )->count(),



            'completed'=>
                Order::where(
                    'status',
                    'completed'
                )->count(),


        ]);

    }



}