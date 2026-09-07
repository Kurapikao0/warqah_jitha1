<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function __construct(
        protected CartService $service
    ) {}

    public function store(
        AddCartItemRequest $request
    ) {

        $cart =
        $this->service
            ->getCart(auth()->id());

        $item =
        $this->service
            ->addItem([

                'cart_id' => $cart->id,

                ...$request->validated(),

            ]);

        return response()->json($item, 201);

    }

    public function update(
        UpdateCartItemRequest $request,
        CartItem $cartItem
    ) {

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
            'message' => 'Cart updated',
            'data' => new CartItemResource($cartItem->fresh('product.media')),
        ]);

    }

    public function rereserve(Request $request, CartItem $cartItem)
    {
        abort_if(
            $cartItem->cart?->customer_id !== auth()->id(),
            403
        );

        $durationMinutes = max(1, (int) $request->input('duration_minutes', 5));
        $item = $this->service->renewItemReservation($cartItem, $durationMinutes);

        return response()->json([
            'message' => 'Cart item reservation renewed',
            'data' => new CartItemResource($item->load('product.media')),
        ]);
    }

    public function destroy(
        CartItem $cartItem
    ) {

        abort_if(
            $cartItem->cart?->customer_id !== auth()->id(),
            403
        );

        $this->service
            ->removeItem($cartItem);

        return response()->json([

            'message' => 'Item removed',

        ]);

    }
}
