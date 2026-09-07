<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\StockReservation;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CartRepository implements CartRepositoryInterface
{
    public const DEFAULT_RESERVATION_MINUTES = 5;

    public function cleanupExpiredReservations(?int $customerId = null): int
    {
        $now = now();
        $query = StockReservation::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now);

        if ($customerId !== null) {
            $query->where('customer_id', $customerId);
        }

        $expiredReservations = $query->lockForUpdate()->get();

        if ($expiredReservations->isEmpty()) {
            return 0;
        }

        $affectedProductIds = [];

        foreach ($expiredReservations as $reservation) {
            $reservation->update(['status' => 'expired']);
            $affectedProductIds[] = $reservation->product_id;

            if ($reservation->cart_item_id) {
                CartItem::where('id', $reservation->cart_item_id)->update([
                    'reserved_at' => null,
                    'expires_at' => null,
                ]);
            }
        }

        $uniqueProductIds = array_unique(array_filter($affectedProductIds));
        foreach ($uniqueProductIds as $productId) {
            $product = Product::lockForUpdate()->find($productId);
            if ($product) {
                $product->recalculateReservedQuantity();
            }
        }

        return count($expiredReservations);
    }

    public function getCustomerCart($customerId)
    {
        DB::transaction(function () use ($customerId) {
            $this->cleanupExpiredReservations($customerId);
        });

        return Cart::with([
            'items.product.media',
        ])
            ->where('customer_id', $customerId)
            ->first();
    }

    public function createCart($customerId)
    {
        return Cart::create([
            'customer_id' => $customerId,
        ]);
    }

    public function addItem(array $data)
    {
        return DB::transaction(function () use ($data) {
            $cartId = $data['cart_id'];
            $productId = $data['product_id'];
            $quantity = max(1, (int) ($data['quantity'] ?? 1));
            $customerId = auth('customer')->id() ?? auth()->id();

            $this->cleanupExpiredReservations();

            $product = Product::lockForUpdate()->findOrFail($productId);
            $availableStock = max(0, (int) $product->stock_quantity - (int) ($product->reserved_quantity ?? 0));

            $existingItem = CartItem::where('cart_id', $cartId)
                ->where('product_id', $productId)
                ->first();

            $expiresAt = now()->addMinutes(self::DEFAULT_RESERVATION_MINUTES);

            if ($existingItem) {
                if ($availableStock < $quantity) {
                    abort(422, 'الكمية المطلوبة غير متوفرة في المخزون المتاح للمنتج: ' . $product->name);
                }

                $newTotalQty = (int) $existingItem->quantity + $quantity;
                $product->increment('reserved_quantity', $quantity);

                $existingItem->update([
                    'quantity' => $newTotalQty,
                    'customization_note' => $data['customization_note'] ?? $existingItem->customization_note,
                    'reserved_at' => now(),
                    'expires_at' => $expiresAt,
                ]);

                StockReservation::updateOrCreate(
                    [
                        'cart_item_id' => $existingItem->id,
                        'product_id' => $productId,
                    ],
                    [
                        'customer_id' => $customerId,
                        'quantity' => $newTotalQty,
                        'reserved_at' => now(),
                        'expires_at' => $expiresAt,
                        'status' => 'active',
                    ]
                );

                return $existingItem->load('product.media');
            }

            if ($availableStock < $quantity) {
                abort(422, 'الكمية المطلوبة غير متوفرة في المخزون المتاح للمنتج: ' . $product->name);
            }

            $data['reserved_at'] = now();
            $data['expires_at'] = $expiresAt;

            $cartItem = CartItem::create($data);

            $product->increment('reserved_quantity', $quantity);

            StockReservation::create([
                'product_id' => $product->id,
                'cart_item_id' => $cartItem->id,
                'customer_id' => $customerId,
                'quantity' => $quantity,
                'reserved_at' => now(),
                'expires_at' => $expiresAt,
                'status' => 'active',
            ]);

            return $cartItem->load('product.media');
        });
    }

    public function updateItem($item, array $data)
    {
        return DB::transaction(function () use ($item, $data) {
            $customerId = auth('customer')->id() ?? auth()->id();
            $this->cleanupExpiredReservations($customerId);
            $item = CartItem::whereKey($item->id)->lockForUpdate()->firstOrFail();

            if (isset($data['quantity'])) {
                $newQuantity = max(1, (int) $data['quantity']);
                $product = Product::lockForUpdate()->findOrFail($item->product_id);
                $reservation = StockReservation::where('cart_item_id', $item->id)
                    ->where('status', 'active')
                    ->where(function ($query) {
                        $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })
                    ->lockForUpdate()
                    ->first();
                $reservedQuantity = $reservation ? (int) $reservation->quantity : 0;
                $diff = $newQuantity - $reservedQuantity;

                if ($diff > 0) {
                    $availableStock = max(0, (int) $product->stock_quantity - (int) ($product->reserved_quantity ?? 0));
                    if ($availableStock < $diff) {
                        abort(422, 'الكمية المطلوبة غير متوفرة في المخزون المتاح للمنتج: ' . $product->name);
                    }
                    $product->increment('reserved_quantity', $diff);
                } elseif ($diff < 0) {
                    $product->decrement('reserved_quantity', min(abs($diff), $reservedQuantity));
                }

                $expiresAt = now()->addMinutes(self::DEFAULT_RESERVATION_MINUTES);
                $data['reserved_at'] = now();
                $data['expires_at'] = $expiresAt;

                StockReservation::updateOrCreate(
                    [
                        'cart_item_id' => $item->id,
                        'product_id' => $item->product_id,
                    ],
                    [
                        'customer_id' => $customerId,
                        'quantity' => $newQuantity,
                        'reserved_at' => now(),
                        'expires_at' => $expiresAt,
                        'status' => 'active',
                    ]
                );
            }

            return $item->update($data);
        });
    }

    public function removeItem($item)
    {
        return DB::transaction(function () use ($item) {
            $customerId = auth('customer')->id() ?? auth()->id();
            $this->cleanupExpiredReservations($customerId);
            $item = CartItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            $product = Product::lockForUpdate()->find($item->product_id);
            $reservation = StockReservation::where('cart_item_id', $item->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if ($product && $reservation) {
                $product->decrement('reserved_quantity', min(
                    (int) $product->reserved_quantity,
                    (int) $reservation->quantity
                ));
                $reservation->update(['status' => 'released']);
            }

            return $item->delete();
        });
    }

    public function clearCart($customerId)
    {
        return DB::transaction(function () use ($customerId) {
            $cart = Cart::with('items')->where('customer_id', $customerId)->first();
            if (! $cart) {
                return true;
            }

            foreach ($cart->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product) {
                    $newReserved = max(0, (int) ($product->reserved_quantity ?? 0) - (int) $item->quantity);
                    $product->update(['reserved_quantity' => $newReserved]);
                }

                StockReservation::where('cart_item_id', $item->id)
                    ->where('status', 'active')
                    ->update(['status' => 'released']);

                $item->delete();
            }

            return true;
        });
    }

    public function renewReservation($customerId, int $durationMinutes = self::DEFAULT_RESERVATION_MINUTES)
    {
        return DB::transaction(function () use ($customerId, $durationMinutes) {
            $cart = Cart::with('items')->where('customer_id', $customerId)->first();
            if (! $cart || $cart->items->isEmpty()) {
                return false;
            }

            $expiresAt = now()->addMinutes($durationMinutes);

            foreach ($cart->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if (! $product) {
                    continue;
                }

                $reservation = StockReservation::where('cart_item_id', $item->id)->first();

                if (! $reservation || $reservation->status !== 'active' || $reservation->isExpired()) {
                    $availableStock = max(0, (int) $product->stock_quantity - (int) ($product->reserved_quantity ?? 0));
                    if ($availableStock < $item->quantity) {
                        abort(422, 'لا يتوفر مخزون كافٍ لإعادة حجز المنتج: ' . $product->name);
                    }
                    $product->increment('reserved_quantity', $item->quantity);
                }

                $item->update([
                    'reserved_at' => now(),
                    'expires_at' => $expiresAt,
                ]);

                StockReservation::updateOrCreate(
                    [
                        'cart_item_id' => $item->id,
                        'product_id' => $item->product_id,
                    ],
                    [
                        'customer_id' => $customerId,
                        'quantity' => $item->quantity,
                        'reserved_at' => now(),
                        'expires_at' => $expiresAt,
                        'status' => 'active',
                    ]
                );
            }

            return true;
        });
    }

    public function renewItemReservation($item, int $durationMinutes = self::DEFAULT_RESERVATION_MINUTES)
    {
        return DB::transaction(function () use ($item, $durationMinutes) {
            $customerId = auth('customer')->id() ?? auth()->id();
            $this->cleanupExpiredReservations($customerId);

            $cartItem = CartItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            $product = Product::lockForUpdate()->findOrFail($cartItem->product_id);
            $reservation = StockReservation::where('cart_item_id', $cartItem->id)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->lockForUpdate()
                ->first();

            if (! $reservation) {
                $availableStock = max(0, (int) $product->stock_quantity - (int) ($product->reserved_quantity ?? 0));
                if ($availableStock < (int) $cartItem->quantity) {
                    abort(422, 'لا يتوفر مخزون كافٍ لإعادة حجز المنتج: ' . $product->name . '. المتاح حالياً: ' . $availableStock);
                }
                $product->increment('reserved_quantity', (int) $cartItem->quantity);
            }

            $expiresAt = now()->addMinutes(max(1, $durationMinutes));
            $cartItem->update([
                'reserved_at' => now(),
                'expires_at' => $expiresAt,
            ]);

            StockReservation::updateOrCreate(
                [
                    'cart_item_id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                ],
                [
                    'customer_id' => $customerId,
                    'quantity' => $cartItem->quantity,
                    'reserved_at' => now(),
                    'expires_at' => $expiresAt,
                    'status' => 'active',
                ]
            );

            return $cartItem->fresh('product.media');
        });
    }

    public function syncCart($customerId, array $items)
    {
        return DB::transaction(function () use ($customerId, $items) {
            $cart = Cart::firstOrCreate(['customer_id' => $customerId]);

            foreach ($items as $itemData) {
                $productId = $itemData['product_id'] ?? null;
                $quantity = max(1, (int) ($itemData['quantity'] ?? 1));
                if (! $productId) {
                    continue;
                }

                $this->addItem([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'customization_note' => $itemData['customization_note'] ?? null,
                ]);
            }

            return $this->getCustomerCart($customerId);
        });
    }
}
