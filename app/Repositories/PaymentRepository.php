<?php

namespace App\Repositories;


use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;



class PaymentRepository implements PaymentRepositoryInterface
{


    public function getAll()
    {

        return Payment::with([

            'order',
            'order.customer'

        ])
        ->latest()
        ->paginate(20);

    }




    public function getCustomerPayments($customerId)
    {

        return Payment::with([

            'order'

        ])
        ->whereHas(
            'order',
            function($query) use($customerId){

                $query->where(
                    'customer_id',
                    $customerId
                );

            }
        )
        ->latest()
        ->paginate(15);


    }





    public function findById($id)
    {

        return Payment::with([

            'order',
            'order.items'

        ])
        ->findOrFail($id);

    }





    public function create(array $data)
    {

        return Payment::create($data);

    }





    public function update(
        Payment $payment,
        array $data
    )
    {

        return $payment->update($data);

    }


}