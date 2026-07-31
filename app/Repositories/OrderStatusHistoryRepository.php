<?php

namespace App\Repositories;


use App\Models\OrderStatusHistory;
use App\Repositories\Contracts\OrderStatusHistoryRepositoryInterface;



class OrderStatusHistoryRepository implements OrderStatusHistoryRepositoryInterface
{


    public function getByOrder($orderId)
    {

        return OrderStatusHistory::with(
            'changedBy'
        )
        ->where(
            'order_id',
            $orderId
        )
        ->latest()
        ->get();


    }





    public function create(array $data)
    {

        return OrderStatusHistory::create($data);

    }


}