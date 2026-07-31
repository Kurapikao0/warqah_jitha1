<?php

namespace App\Http\Controllers\API\Admin;


use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Services\OrderStatusHistoryService;
use App\Http\Resources\OrderStatusHistoryResource;



class OrderStatusHistoryController extends Controller
{


public function __construct(
protected OrderStatusHistoryService $service
)
{}




public function index(Order $order)
{


return OrderStatusHistoryResource::collection(

$this->service
->orderHistory($order->id)

);


}


}