<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service
    ) {}

    public function index()
    {

        return OrderResource::collection(

            $this->service
                ->customerOrders(auth('customer')->id())

        );

    }

    public function store(StoreOrderRequest $request)
    {

        $data = $request->validated();

        $data['customer_id'] = auth('customer')->id();

        $order =
        $this->service->create($data);

        $this->authorize('view', $order);

        return new OrderResource($order);

    }

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
