<?php

namespace App\Http\Controllers\API\Customer;


use App\Models\Order;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Requests\Order\StoreOrderRequest;



class OrderController extends Controller
{


public function __construct(
protected OrderService $service
)
{}




public function index()
{

return OrderResource::collection(

$this->service
->customerOrders(auth()->id())

);

}




public function store(StoreOrderRequest $request)
{


$data=$request->validated();


$data['customer_id']=auth()->id();


$order =
$this->service->create($data);



return new OrderResource($order);


}




public function show($id)
{

return new OrderResource(

$this->service->find($id)

);

}


}