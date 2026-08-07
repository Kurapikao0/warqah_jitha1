<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Http\Resources\CartResource;
use Illuminate\Http\Response;

class CartController extends Controller
{
    public function __construct(
        protected CartService $service
    ) {}

    public function index()
    {
        $cart = $this->service->getCart(auth()->id());

        return response()->json([
            'data' => new CartResource($cart)
        ], Response::HTTP_OK); // 
    }
}