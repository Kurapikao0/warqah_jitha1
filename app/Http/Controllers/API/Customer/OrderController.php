<?php

namespace App\Http\Controllers\API\Customer;


use App\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Requests\Order\StoreOrderRequest;
use Illuminate\Http\Request;


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
->customerOrders(auth('customer')->id())

);

}




public function store(StoreOrderRequest $request)
{


$data=$request->validated();


$data['customer_id'] = auth('customer')->id();


$order =
$this->service->create($data);

$this->authorize('view', $order);

return new OrderResource($order);


}




/*public function show($id)
{

return new OrderResource(

$this->service->find($id)

);

}*/

public function show($id)
{
    
    $order = $this->service->findCustomerOrder(
        auth('customer')->id(),
        $id
    );
    $this->authorize('view', $order);
    return new OrderResource($order);
}

}