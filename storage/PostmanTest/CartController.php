<?php

namespace App\Http\Controllers\API\Customer;


use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Http\Resources\CartResource;



class CartController extends Controller
{


public function __construct(
protected CartService $service
)
{}




public function index()
{


return new CartResource(

$this->service
->getCart(
auth()->id()
)

);


}



}