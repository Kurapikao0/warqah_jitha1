<?php

namespace App\Services;


use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\CartRepositoryInterface;



class CartService
{


public function __construct(
protected CartRepositoryInterface $repository
)
{}





public function getCart($customerId)
{


$cart =
$this->repository
->getCustomerCart($customerId);



if(!$cart)
{

$cart =
$this->repository
->createCart($customerId);

}


return $cart;

}




public function addItem(
array $data
)
{

return DB::transaction(function()
use($data){


return $this->repository
->addItem($data);


});


}






public function updateItem(
$item,
array $data
)
{


return $this->repository
->updateItem(
$item,
$data
);


}





public function removeItem($item)
{

return $this->repository
->removeItem($item);

}


}