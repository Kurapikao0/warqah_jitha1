<?php

namespace App\Http\Controllers\API\Customer;


use App\Models\CartItem;
use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Http\Requests\Cart\AddCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;



class CartItemController extends Controller
{


public function __construct(
protected CartService $service
)
{}





public function store(
AddCartItemRequest $request
)
{


$cart =
$this->service
->getCart(auth()->id());



$item =
$this->service
->addItem([

'cart_id'=>$cart->id,

...$request->validated()

]);



return response()->json($item,201);

}




public function update(
UpdateCartItemRequest $request,
CartItem $cartItem
)
{

abort_if(
    $cartItem->cart?->customer_id !== auth()->id(),
    403
);

$this->service
->updateItem(
$cartItem,
$request->validated()
);


return response()->json([

'message'=>'Cart updated'

]);


}





public function destroy(
CartItem $cartItem
)
{

abort_if(
    $cartItem->cart?->customer_id !== auth()->id(),
    403
);

$this->service
->removeItem($cartItem);



return response()->json([

'message'=>'Item removed'

]);


}



}