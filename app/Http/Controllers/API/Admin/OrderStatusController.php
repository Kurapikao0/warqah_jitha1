<?php

namespace App\Http\Controllers\API\Admin;


use App\Models\Order;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;


class OrderStatusController extends Controller
{


public function __construct(
protected OrderService $service
)
{}




public function update(
UpdateOrderStatusRequest $request,
Order $order
)
{


$this->service
->updateStatus(
$order,
$request->validated()
);



return response()->json([

'message'=>
'Order status updated'

]);


}

public function updateStatus(
    Order $order,
    array $data
)
{
    return DB::transaction(function () use ($order, $data) {

        $order->update([
            'status' => OrderStatus::from($data['status']),
        ]);

        $order->statusHistory()->create([
            'status' => $data['status'],
            'note' => $data['note'] ?? null,
            'changed_by' => auth()->id(),
        ]);

        return $order->refresh();
    });
}

}