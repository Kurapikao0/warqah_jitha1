<?php

namespace App\Services;


use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Models\Product;
use App\Models\Address;
use Illuminate\Support\Str;
use App\Enums\OrderStatus;


class OrderService
{


public function __construct(
protected OrderRepositoryInterface $repository
)
{}




public function all()
{

return $this->repository->getAll();

}





public function customerOrders($customerId)
{

return $this->repository
->getCustomerOrders($customerId);

}





public function find($id)
{

return $this->repository
->findById($id);

}






/*public function create(array $data)
{


return DB::transaction(function()
use($data){


$order =
$this->repository
->create($data);



$order->statusHistory()
->create([

'status' => OrderStatus::Received
'note'=>'Order created',

]);



return $order;


});


}*/



/*public function create(array $data)
{
    return DB::transaction(function () use ($data) {

        $address = Address::where(
            'id',
            $data['address_id']
        )
        ->where(
            'customer_id',
            auth()->id()
        )
        ->firstOrFail();

        $subtotal = 0;

        $items = [];

        foreach ($data['items'] as $item) {

            $product = Product::findOrFail(
                $item['product_id']
            );

            $linePrice =
                $product->price *
                $item['quantity'];

            $subtotal += $linePrice;

            $items[] = [

                'product' => $product,

                'quantity' => $item['quantity'],

                'customization_id' =>
                    $item['customization_id']
                    ?? null,

                'unit_price' =>
                    $product->price,

            ];
        }

        $shipping = 0;

        $order = $this->repository->create([

            'order_number' =>
                'ORD-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),

            'customer_id' =>
                auth()->id(),

            'address_id' =>
                $address->id,

            'shipping_recipient_name' =>
                $address->recipient_name,

            'shipping_phone' =>
                $address->phone,

            'shipping_address_full' =>
                trim(
                    $address->street . ', ' .
                    $address->district
                ),

            'shipping_city' =>
                $address->city,

            'shipping_country' =>
                $address->country,

            'order_type' =>
                $data['order_type'],

            'status' =>
                OrderStatus::Received,

            'subtotal' =>
                $subtotal,

            'shipping_fee' =>
                $shipping,

            'total_amount' =>
                $subtotal + $shipping,

        ]);

        foreach ($items as $item) {

            $this->repository->createItem([

                'order_id' =>
                    $order->id,

                'product_id' =>
                    $item['product']->id,

                'product_customization_request_id' =>
                    $item['customization_id'],

                'quantity' =>
                    $item['quantity'],

                'unit_price' =>
                    $item['unit_price'],

                'is_customized' =>
                    $item['customization_id'] != null,

            ]);
        }

        $order->statusHistory()->create([

            'status' =>
                OrderStatus::Received,

            'note' =>
                'Order Created',

            'changed_by' =>
                auth()->id(),

        ]);

        return $order->load([
            'items.product',
            'payment',
            'customer'
        ]);
    });
}*/





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

                // ===== هنا سادساً: التحقق من المخزون =====
                if ($product->stock_quantity < $item['quantity']) {
                    abort(422, 'Insufficient stock for product: ' . $product->name);
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
                'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
                'customer_id' => auth('customer')->id(),
                'address_id' => $address->id,
                'shipping_recipient_name' => $address->recipient_name,
                'shipping_phone' => $address->phone,
                'shipping_address_full' => trim(
                    ($address->street ?? '') . ', ' . ($address->district ?? '')
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
)
{


return DB::transaction(function()
use($order,$data){



/*$order->update([

'status'=>$data['status']

]);*/
$order->update([
    'status' => OrderStatus::from($data['status'])
]);


$order->statusHistory()
->create([

'status' => OrderStatus::from($data['status']),
'note'=>$data['note'] ?? null,

'changed_by' => auth('admin')->id(),
]);



return $order;


});


}

public function findCustomerOrder(
    int $customerId,
    int $orderId )
{
    return $this->repository
        ->findCustomerOrder(
            $customerId,
            $orderId
        );
} 
}