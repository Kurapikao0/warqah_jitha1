<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function getAll(int $perPage = 20)
    {

        return Order::with([

            'customer',

            'items.product',

            'payment',

            'currentProductionStage',

        ])
            ->latest()
            ->paginate($perPage);

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

    public function statistics($from = null, $to = null)
    {
        $querySalesTimeseries = Order::whereHas('payment', function ($query) {
            $query->where('status', 'paid');
        });

        $queryTotalRevenue = Order::whereHas('payment', function ($query) {
            $query->where('status', 'paid');
        });

        if ($from) {
            $querySalesTimeseries->whereDate('created_at', '>=', $from);
            $queryTotalRevenue->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $querySalesTimeseries->whereDate('created_at', '<=', $to);
            $queryTotalRevenue->whereDate('created_at', '<=', $to);
        }

        $salesTimeseries = $querySalesTimeseries
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->take(30)
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'revenue' => (float) $item->revenue,
                ];
            })
            ->values()
            ->toArray();

        $totalRevenue = $queryTotalRevenue->sum('total_amount');
        
        $paidOrdersCount = Order::whereHas('payment', function ($query) {
            $query->where('status', 'paid');
        });
        
        $baseOrdersQuery = Order::query();
        if ($from) {
            $paidOrdersCount->whereDate('created_at', '>=', $from);
            $baseOrdersQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $paidOrdersCount->whereDate('created_at', '<=', $to);
            $baseOrdersQuery->whereDate('created_at', '<=', $to);
        }
        
        $paidOrdersCount = $paidOrdersCount->count();

        $avgOrderValue = $paidOrdersCount > 0 ? (float) $totalRevenue / $paidOrdersCount : 0;

        return [

            'total_orders' => (clone $baseOrdersQuery)->count(),

            'pending' => (clone $baseOrdersQuery)->where(
                'status',
                'received'
            )->count(),

            'production' => (clone $baseOrdersQuery)->where(
                'status',
                'in_production'
            )->count(),

            'completed' => (clone $baseOrdersQuery)->where(
                'status',
                'completed'
            )->count(),

            'total_revenue' => (float) $totalRevenue,
            
            'paid_orders_count' => $paidOrdersCount,
            
            'avg_order_value' => (float) $avgOrderValue,

            'sales_timeseries' => $salesTimeseries,

        ];
    }
}
