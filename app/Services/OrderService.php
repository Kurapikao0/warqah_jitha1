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

    /**
     * Create an order from the admin panel.
     *
     * Unlike the customer checkout flow, the admin chooses the customer
     * directly. The customer's default/first address is used when one exists.
     */
    public function createForAdmin(array $data)
    {
        return DB::transaction(function () use ($data) {
            $customer = \App\Models\Customer::query()
                ->findOrFail((int) $data['customer_id']);

            $address = $customer->addresses()
                ->where('is_default', true)
                ->first()
                ?? $customer->addresses()->first();

            $subtotal = 0.0;
            $preparedItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::query()
                    ->whereKey((int) $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $quantity = (int) $item['quantity'];
                $availableStock = max(
                    0,
                    (int) $product->stock_quantity - (int) $product->reserved_quantity
                );

                if ($availableStock < $quantity) {
                    abort(
                        422,
                        'الكمية المطلوبة من المنتج غير متاحة حالياً: '.$product->name
                    );
                }

                $customizationId = $item['customization_id'] ?? null;
                $customizationNote = trim((string) ($item['customization_note'] ?? ''));
                $hasCustomization = $customizationId !== null || $customizationNote !== '';

                if (
                    ($data['order_type'] ?? 'ready_made') === 'custom'
                    && ! $product->is_customizable
                ) {
                    abort(422, 'هذا المنتج لا يدعم التخصيص: '.$product->name);
                }

                if ($hasCustomization && ! $product->is_customizable) {
                    abort(422, 'لا يمكن تخصيص هذا المنتج: '.$product->name);
                }

                if ($customizationId !== null) {
                    $requestExists = \App\Models\ProductCustomizationRequest::query()
                        ->whereKey((int) $customizationId)
                        ->where(function ($query) use ($customer, $product) {
                            $query->whereNull('customer_id')
                                ->orWhere('customer_id', $customer->id);
                        })
                        ->where(function ($query) use ($product) {
                            $query->whereNull('base_product_id')
                                ->orWhere('base_product_id', $product->id);
                        })
                        ->exists();

                    if (! $requestExists) {
                        abort(422, 'طلب التخصيص المحدد غير صالح لهذا العميل أو المنتج.');
                    }
                }

                $unitPrice = (float) $product->price;
                $subtotal += $unitPrice * $quantity;

                $preparedItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'customization_id' => $customizationId,
                    'customization_note' => $customizationNote !== '' ? $customizationNote : null,
                    'unit_price' => $unitPrice,
                    'is_customized' => $hasCustomization || ($data['order_type'] ?? null) === 'custom',
                ];
            }

            $shippingFee = max(0.0, (float) ($data['shipping_fee'] ?? 0));
            $total = $subtotal + $shippingFee;

            $order = $this->repository->create([
                'order_number' => 'ORD-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
                'customer_id' => $customer->id,
                'address_id' => $address?->id,
                'shipping_recipient_name' => $address?->recipient_name ?? $customer->full_name,
                'shipping_phone' => $address?->phone ?? $customer->phone ?? '-',
                'shipping_address_full' => $address
                    ? trim(implode(', ', array_filter([
                        $address->street,
                        $address->district,
                    ])))
                    : 'لم يتم تحديد عنوان شحن',
                'shipping_city' => $address?->city ?? 'غير محدد',
                'shipping_country' => $address?->country ?? 'Yemen',
                'order_type' => $data['order_type'],
                'status' => OrderStatus::Received,
                'current_production_stage_id' => \App\Models\OrderProductionStage::query()
                    ->orderBy('sort_order')
                    ->value('id'),
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total_amount' => $total,
            ]);

            foreach ($preparedItems as $item) {
                $this->repository->createItem([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_customization_request_id' => $item['customization_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'customization_note' => $item['customization_note'],
                    'is_customized' => $item['is_customized'],
                ]);

                // Auto-Decrement Stock
                $item['product']->decrement('stock_quantity', $item['quantity']);
                $item['product']->refresh();

                if ($item['product']->stock_quantity <= 5) {
                    $admins = \App\Models\Admin::all();
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\LowStockNotification($item['product']));
                }
            }

            $order->statusHistory()->create([
                'status' => OrderStatus::Received,
                'note' => 'Order created from admin panel',
                'changed_by' => auth('admin')->id(),
            ]);

            $stageId = $order->current_production_stage_id;
            if ($stageId) {
                $order->productionStageHistory()->create([
                    'stage_id' => $stageId,
                    'changed_by' => auth('admin')->id(),
                ]);
            }

            return $order->load([
                'customer',
                'items.product',
                'payment',
                'statusHistory',
                'productionStageHistory',
                'currentProductionStage',
                'address',
            ]);
        });
    }

    public function updateStatus(
        Order $order,
        array $data
    ) {

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
