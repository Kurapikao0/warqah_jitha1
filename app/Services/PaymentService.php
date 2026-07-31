<?php

namespace App\Services;


use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\PaymentRepositoryInterface;



class PaymentService
{


    public function __construct(
        protected PaymentRepositoryInterface $repository
    )
    {

    }





    public function all()
    {

        return $this->repository->getAll();

    }





    public function customerPayments($customerId)
    {

        return $this->repository
        ->getCustomerPayments($customerId);

    }





    public function find($id)
    {

        return $this->repository
        ->findById($id);

    }





    public function create(array $data)
    {


        return DB::transaction(function()
        use($data){


            return $this->repository
            ->create($data);


        });


    }





    public function updateStatus(
        Payment $payment,
        array $data
    )
    {


        return DB::transaction(function()
        use($payment,$data){



            return $this->repository
            ->update(
                $payment,
                $data
            );


        });


    }


}