<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockReservation;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $repository
    ) {}

    public function all(int $perPage = 20)
    {
        return $this->repository->getAll($perPage);
    }

    public function statistics($from = null, $to = null)
    {
        return $this->repository->statistics($from, $to);
    }

    public function customerOrders(int $customerId)
    {
        return $this->repository->getCustomerOrders($customerId);
    }

    public function find(int $id)
    {
        return $this->repository->findById($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $customerId = auth('customer')->id() ?? $data['customer_id'] ?? null;

            // 1) Verify shipping address
            $address = Address::where('id', $data['address_id'])
                ->where('customer_id', $customerId)
                ->firstOrFail();

            $subtotal = 0;
            $items = [];
            $productsToProcess = [];

            // 2) Validate stock and reservations
            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $requestedQty = max(1, (int) $item['quantity']);

                if (
                    ($data['order_type'] ?? '') === 'custom'
                    && ! $product->is_customizable
                ) {
                    abort(422, 'هذا المنتج غير قابل للتخصيص: ' . $product->name);
                }
                if (
                    ! empty($item['customization_id']) &&
                    ! $product->is_customizable
                ) {
                    abort(422, 'هذا المنتج غير قابل للتخصيص: ' . $product->name);
                }

                // Check active reservation for this customer
                $activeReservation = null;
                if ($customerId) {
                    $activeReservation = StockReservation::where('product_id', $product->id)
                        ->where('customer_id', $customerId)
                        ->where('status', 'active')
                        ->where(function ($q) {
                            $q->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                        })
                        ->latest()
                        ->first();
                }

                $reservedForMe = $activeReservation ? (int) $activeReservation->quantity : 0;
                $generalAvailable = max(0, (int) $product->stock_quantity - (int) ($product->reserved_quantity ?? 0));
                $effectiveAvailable = $generalAvailable + $reservedForMe;

                if ($effectiveAvailable < $requestedQty) {
                    abort(422, 'الكمية المطلوبة غير متوفرة في المخزون للمنتج: ' . $product->name);
                }

                $linePrice = $product->price * $requestedQty;
                $subtotal += $linePrice;

                $items[] = [
                    'product' => $product,
                    'quantity' => $requestedQty,
                    'customization_id' => $item['customization_id'] ?? null,
                    'unit_price' => $product->price,
                    'reservation' => $activeReservation,
                ];
            }

            $shipping = 0;

            // 3) Create Order
            $order = $this->repository->create([
                'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
                'customer_id' => $customerId,
                'address_id' => $address->id,
                'shipping_recipient_name' => $address->recipient_name,
                'shipping_phone' => $address->phone,
                'shipping_address_full' => trim(
                    ($address->street ?? '') . ', ' . ($address->district ?? '')
                ),
                'shipping_city' => $address->city,
                'shipping_country' => $address->country,
                'order_type' => $data['order_type'] ?? 'ready_made',
                'status' => OrderStatus::Received,
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping,
                'total_amount' => $subtotal + $shipping,
            ]);

            // 4) Create Order Items, consume reservations, and decrement stock
            $cart = $customerId ? Cart::where('customer_id', $customerId)->first() : null;

            foreach ($items as $item) {
                $product = $item['product'];
                $qty = (int) $item['quantity'];
                $reservation = $item['reservation'];

                $this->repository->createItem([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_customization_request_id' => $item['customization_id'],
                    'quantity' => $qty,
                    'unit_price' => $item['unit_price'],
                    'is_customized' => $item['customization_id'] != null,
                ]);

                if ($reservation) {
                    $reservation->update([
                        'status' => 'consumed',
                        'order_id' => $order->id,
                    ]);

                    if ($reservation->cart_item_id) {
                        CartItem::where('id', $reservation->cart_item_id)->delete();
                    } elseif ($cart) {
                        CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $product->id)
                            ->delete();
                    }
                } elseif ($cart) {
                    CartItem::where('cart_id', $cart->id)
                        ->where('product_id', $product->id)
                        ->delete();
                }

                // Decrement actual physical stock
                $newStock = max(0, (int) $product->stock_quantity - $qty);
                $product->update(['stock_quantity' => $newStock]);

                // Recalculate reserved quantity
                $product->recalculateReservedQuantity();
            }

            // 5) Create Status History
            $order->statusHistory()->create([
                'status' => OrderStatus::Received,
                'note' => 'Order created successfully',
                'changed_by' => $customerId,
            ]);

            return $order->load([
                'customer',
                'items.product',
                'payment',
                'statusHistory',
            ]);
        });
    }

    public function updateStatus(Order $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            $newStatus = OrderStatus::from($data['status']);
            $oldStatus = $order->status;

            $order->update([
                'status' => $newStatus,
            ]);

            $order->statusHistory()->create([
                'status' => $newStatus,
                'note' => $data['note'] ?? null,
                'changed_by' => auth('admin')->id() ?? auth()->id(),
            ]);

            // If cancelled from an active order, restore physical stock
            if ($newStatus === OrderStatus::Cancelled && $oldStatus !== OrderStatus::Cancelled) {
                foreach ($order->items as $orderItem) {
                    $product = Product::lockForUpdate()->find($orderItem->product_id);
                    if ($product) {
                        $product->increment('stock_quantity', $orderItem->quantity);
                        $product->recalculateReservedQuantity();
                    }
                }
            }

            return $order;
        });
    }

    public function findCustomerOrder(int $customerId, int $orderId)
    {
        return $this->repository->findCustomerOrder($customerId, $orderId);
    }

    public function delete(Order $order)
    {
        return $order->delete();
    }
}
