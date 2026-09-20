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

    private function activeReservationForItem(int $cartItemId): ?StockReservation
    {
        $reservations = StockReservation::where('cart_item_id', $cartItemId)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->lockForUpdate()
            ->orderByDesc('id')
            ->get();

        $reservation = $reservations->first();

        if ($reservations->count() > 1) {
            $reservations->skip(1)->each(
                fn (StockReservation $duplicate) => $duplicate->update(['status' => 'released'])
            );
        }

        return $reservation;
    }

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
            'items.stockReservation',
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

            $existingItem = CartItem::where('cart_id', $cartId)
                ->where('product_id', $productId)
                ->first();

            $expiresAt = now()->addMinutes(self::DEFAULT_RESERVATION_MINUTES);

            if ($existingItem) {
                $newTotalQty = (int) $existingItem->quantity + $quantity;
                $reservation = $this->activeReservationForItem($existingItem->id);
                $product->recalculateReservedQuantity();
                $availableStock = max(0, (int) $product->stock_quantity - (int) ($product->reserved_quantity ?? 0));
                $reservedQuantity = (int) ($reservation?->reserved_quantity ?? 0);
                $targetReservedQuantity = min($newTotalQty, $reservedQuantity + $availableStock);

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
                        'reserved_quantity' => $targetReservedQuantity,
                        'reserved_at' => now(),
                        'expires_at' => $expiresAt,
                        'status' => 'active',
                    ]
                );
                $product->recalculateReservedQuantity();

                return $existingItem->load(['product.media', 'stockReservation']);
            }

            $data['reserved_at'] = now();
            $data['expires_at'] = $expiresAt;
            $availableStock = max(0, (int) $product->stock_quantity - (int) ($product->reserved_quantity ?? 0));

            $cartItem = CartItem::create($data);

            $reservedQuantity = min($quantity, $availableStock);

            StockReservation::create([
                'product_id' => $product->id,
                'cart_item_id' => $cartItem->id,
                'customer_id' => $customerId,
                'quantity' => $quantity,
                'reserved_quantity' => $reservedQuantity,
                'reserved_at' => now(),
                'expires_at' => $expiresAt,
                'status' => 'active',
            ]);
            $product->recalculateReservedQuantity();

            return $cartItem->load(['product.media', 'stockReservation']);
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
                $reservation = $this->activeReservationForItem($item->id);
                $product->recalculateReservedQuantity();
                $reservedQuantity = (int) ($reservation?->reserved_quantity ?? 0);
                $availableForItem = max(
                    0,
                    (int) $product->stock_quantity
                    - (int) ($product->reserved_quantity ?? 0)
                    + $reservedQuantity
                );
                $targetReservedQuantity = min($newQuantity, $reservedQuantity + $availableForItem);

                $reservedQuantity = $targetReservedQuantity;
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
                        'reserved_quantity' => $reservedQuantity,
                        'reserved_at' => now(),
                        'expires_at' => $expiresAt,
                        'status' => 'active',
                    ]
                );
                $product->recalculateReservedQuantity();
            }

            $item->update($data);

            return $item->fresh(['product.media', 'stockReservation']);
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
                $reservation->update(['status' => 'released']);
                $product->recalculateReservedQuantity();
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
                    StockReservation::where('cart_item_id', $item->id)
                        ->where('status', 'active')
                        ->lockForUpdate()
                        ->update(['status' => 'released']);
                    $product->recalculateReservedQuantity();
                }

                $item->delete();
            }

            return true;
        });
    }

    public function renewReservation($customerId, int $durationMinutes = self::DEFAULT_RESERVATION_MINUTES)
    {
        return DB::transaction(function () use ($customerId, $durationMinutes) {
            $this->cleanupExpiredReservations($customerId);
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

                $reservation = $this->activeReservationForItem($item->id);

                if (! $reservation || $reservation->status !== 'active' || $reservation->isExpired()) {
                    $availableStock = max(0, (int) $product->stock_quantity - (int) ($product->reserved_quantity ?? 0));
                    $reservedQuantity = min((int) $item->quantity, $availableStock);
                } else {
                    $reservedQuantity = (int) ($reservation->reserved_quantity ?? 0);
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
                        'reserved_quantity' => $reservedQuantity,
                        'reserved_at' => now(),
                        'expires_at' => $expiresAt,
                        'status' => 'active',
                    ]
                );
                $product->recalculateReservedQuantity();
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
            $reservation = $this->activeReservationForItem($cartItem->id);

            if (! $reservation) {
                $availableStock = max(0, (int) $product->stock_quantity - (int) ($product->reserved_quantity ?? 0));
                $reservedQuantity = min((int) $cartItem->quantity, $availableStock);
            } else {
                $reservedQuantity = (int) ($reservation->reserved_quantity ?? 0);
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
                    'reserved_quantity' => $reservedQuantity,
                    'reserved_at' => now(),
                    'expires_at' => $expiresAt,
                    'status' => 'active',
                ]
            );
            $product->recalculateReservedQuantity();

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
