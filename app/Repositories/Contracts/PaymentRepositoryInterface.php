<?php

namespace App\Repositories\Contracts;


use App\Models\Payment;


interface PaymentRepositoryInterface
{


    public function getAll();


    public function getCustomerPayments($customerId);


    public function findById($id);


    public function create(array $data);


    public function update(
        Payment $payment,
        array $data
    );

    public function findCustomerPayment(
    int $customerId,
    int $paymentId
    );

}