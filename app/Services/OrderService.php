<?php

namespace App\Services;


use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\OrderRepositoryInterface;



class OrderService
{


public function __construct(
protected OrderRepositoryInterface $repository
)
{}




public function all()
{

return $this->repository->getAll();

}





public function customerOrders($customerId)
{

return $this->repository
->getCustomerOrders($customerId);

}





public function find($id)
{

return $this->repository
->findById($id);

}






public function create(array $data)
{


return DB::transaction(function()
use($data){


$order =
$this->repository
->create($data);



$order->statusHistory()
->create([

'status'=>'received',

'note'=>'Order created',

]);



return $order;


});


}






public function updateStatus(
Order $order,
array $data
)
{


return DB::transaction(function()
use($order,$data){



$order->update([

'status'=>$data['status']

]);



$order->statusHistory()
->create([

'status'=>$data['status'],

'note'=>$data['note'] ?? null,

'changed_by'=>auth()->id()

]);



return $order;


});


}


}