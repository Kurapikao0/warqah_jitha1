<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\Request;
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
            'data' => new CartResource($cart),
        ], Response::HTTP_OK);
    }

    public function clear()
    {
        $this->service->clearCart(auth()->id());

        return response()->json([
            'message' => 'Cart cleared and stock reservations released',
        ], Response::HTTP_OK);
    }

    public function renew(Request $request)
    {
        $durationMinutes = (int) $request->input('duration_minutes', 5);
        $renewed = $this->service->renewReservation(auth()->id(), $durationMinutes);

        return response()->json([
            'success' => $renewed,
            'message' => $renewed ? 'Stock reservation renewed successfully' : 'No active cart items to renew',
        ], Response::HTTP_OK);
    }

    public function release()
    {
        $this->service->clearCart(auth()->id());

        return response()->json([
            'message' => 'Stock reservation released successfully',
        ], Response::HTTP_OK);
    }

    public function sync(Request $request)
    {
        $items = (array) $request->input('items', []);
        $cart = $this->service->syncCart(auth()->id(), $items);

        return response()->json([
            'message' => 'Cart synchronized successfully',
            'data' => new CartResource($cart),
        ], Response::HTTP_OK);
    }
}
