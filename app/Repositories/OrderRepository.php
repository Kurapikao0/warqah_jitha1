<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function getAll()
    {

        return Order::with([

            'customer',

            'items.product',

            'payment',

            'currentProductionStage',

        ])
            ->latest()
            ->paginate(20);

    }

    public function getCustomerOrders($customerId)
    {

        return Order::with([

            'items.product',

            'payment',

        ])
            ->where(
                'customer_id',
                $customerId
            )
            ->latest()
            ->paginate(15);

    }

    public function findById($id)
    {

        return Order::with([

            'customer',

            'items.product',

            'payment',

            'statusHistory',

            'productionStageHistory',

        ])
            ->findOrFail($id);

    }

    public function create(array $data)
    {

        return Order::create($data);

    }

    public function createItem(array $data): OrderItem
    {
        return OrderItem::create($data);
    }

    public function update(
        Order $order,
        array $data
    ) {

        $order->update($data);

        return $order->refresh();
    }

    public function findCustomerOrder(
        int $customerId,
        int $orderId
    ) {
        return Order::with([
            'items.product',
            'payment',
            'statusHistory',
            'productionStageHistory',
        ])
            ->where('customer_id', $customerId)
            ->findOrFail($orderId);
    }

    public function statistics()
    {
        return [

            'total_orders' => Order::count(),

            'pending' => Order::where(
                'status',
                'received'
            )->count(),

            'production' => Order::where(
                'status',
                'in_production'
            )->count(),

            'completed' => Order::where(
                'status',
                'completed'
            )->count(),

        ];
    }
}
