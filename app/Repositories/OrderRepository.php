<?php

namespace App\Repositories;


use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;



class OrderRepository implements OrderRepositoryInterface
{


public function getAll()
{

return Order::with([

'customer',

'items.product',

'payment',

'currentProductionStage'

])
->latest()
->paginate(20);


}




public function getCustomerOrders($customerId)
{


return Order::with([

'items.product',

'payment'

])

->where(
'customer_id',
$customerId
)

->latest()

->paginate(15);


}




public function findById($id)
{


return Order::with([

'customer',

'items.product',

'payment',

'statusHistory',

'productionHistory'

])

->findOrFail($id);


}




public function create(array $data)
{

return Order::create($data);

}





public function update(
Order $order,
array $data
)
{

return $order->update($data);

}


}