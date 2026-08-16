<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $repository
    ) {}

    public function all()
    {

        return $this->repository->getAll();

    }

    public function statistics()
    {
        return $this->repository->statistics();
    }

    public function customerOrders(int $customerId)
    {

        return $this->repository
            ->getCustomerOrders($customerId);

    }

    public function find(int $id)
    {

        return $this->repository
            ->findById($id);

    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            // 1) التأكد أن العنوان يخص العميل الحالي
            $address = Address::where('id', $data['address_id'])
                ->where('customer_id', auth('customer')->id())
                ->firstOrFail();

            $subtotal = 0;
            $items = [];

            // 2) المرور على المنتجات المطلوبة
            foreach ($data['items'] as $item) {

                $product = Product::findOrFail($item['product_id']);

                // ===== التحقق من أن المنتج يدعم التخصيص =====
                if (
                    $data['order_type'] === 'custom'
                    && ! $product->is_customizable
                ) {
                    abort(422, 'هذا المنتج لا يدعم التخصيص. '.$product->name);
                }
                if (
                    ! empty($item['customization_id']) &&
                    ! $product->is_customizable
                ) {
                    abort(422, 'لا يمكن تخصيص هذا المنتج. '.$product->name);
                }
                // ===== التحقق من المخزون =====
                $availableStock =
                    $product->stock_quantity - $product->reserved_quantity;

                if ($availableStock < $item['quantity']) {
                    abort(422, 'المنتج المطلوب غير متاح حالياً. '.$product->name);
                }

                $linePrice = $product->price * $item['quantity'];

                $subtotal += $linePrice;

                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'customization_id' => $item['customization_id'] ?? null,
                    'unit_price' => $product->price,
                ];
            }

            $shipping = 0;

            // 3) إنشاء الطلب
            $order = $this->repository->create([
                'order_number' => 'ORD-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
                'customer_id' => auth('customer')->id(),
                'address_id' => $address->id,
                'shipping_recipient_name' => $address->recipient_name,
                'shipping_phone' => $address->phone,
                'shipping_address_full' => trim(
                    ($address->street ?? '').', '.($address->district ?? '')
                ),
                'shipping_city' => $address->city,
                'shipping_country' => $address->country,
                'order_type' => $data['order_type'],
                'status' => OrderStatus::Received,
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping,
                'total_amount' => $subtotal + $shipping,
            ]);

            // 4) إنشاء عناصر الطلب
            foreach ($items as $item) {

                $this->repository->createItem([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_customization_request_id' => $item['customization_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'is_customized' => $item['customization_id'] != null,
                ]);

                // ===== هنا سابعاً: حجز الكمية =====
                $item['product']->increment(
                    'reserved_quantity',
                    $item['quantity']
                );
            }

            // 5) إنشاء سجل حالة الطلب
            $order->statusHistory()->create([
                'status' => OrderStatus::Received,
                'note' => 'Order created',
                'changed_by' => auth('customer')->id(),
            ]);

            // ===== هنا ثامناً: تحميل العلاقات قبل الإرجاع =====
            return $order->load([
                'customer',
                'items.product',
                'payment',
                'statusHistory',
            ]);
        });
    }

    public function updateStatus(
        Order $order,
        array $data
    ) {

        return DB::transaction(function () use ($order, $data) {

            /*$order->update([

            'status'=>$data['status']

            ]);*/
            $order->update([
                'status' => OrderStatus::from($data['status']),
            ]);

            $order->statusHistory()
                ->create([

                    'status' => OrderStatus::from($data['status']),
                    'note' => $data['note'] ?? null,

                    'changed_by' => auth('admin')->id(),
                ]);

            return $order;

        });

    }

    public function findCustomerOrder(
        int $customerId,
        int $orderId)
    {
        return $this->repository
            ->findCustomerOrder(
                $customerId,
                $orderId
            );
    }

    public function delete(Order $order)
    {
        return $order->delete();
    }
}
