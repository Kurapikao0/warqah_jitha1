<?php

namespace App\Services;


use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Models\Order;
use Illuminate\Validation\ValidationException;
use App\Enums\PaymentStatus;



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





    /*public function create(array $data)
    {


        return DB::transaction(function()
        use($data){


            return $this->repository
            ->create($data);


        });


    }*/
    public function updateStatus(
        Payment $payment,
        array $data
    )
    {
        return DB::transaction(function()
        use($payment,$data){
        if (PaymentStatus::from($data['status']) === PaymentStatus::Paid) {
            $data['paid_at'] = now();
        }
            return $this->repository
            ->update(
                $payment,
                $data
            );
        });
    }
    public function findCustomerPayment(
    int $customerId,
    int $paymentId
    )
    {
        return $this->repository
            ->findCustomerPayment(
                $customerId,
                $paymentId
            );
    }
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $order = Order::findOrFail($data['order_id']);
            // التأكد أن الطلب يخص العميل الحالي
            if ($order->customer_id !== auth()->id()) {
                throw ValidationException::withMessages([
                    'order_id' => [
                        'You are not allowed to pay for this order.'
                    ],
                ]);
            }
            // منع إنشاء أكثر من عملية دفع لنفس الطلب
            if ($order->payment()->exists()) {
                throw ValidationException::withMessages([
                    'order_id' => [
                        'This order already has a payment.'
                    ],
                ]);
            }
            // لا نثق بالمبلغ القادم من العميل
            $data['amount'] = $order->total_amount;
            // الحالة الافتراضية عند إنشاء الدفع
            $data['status'] = PaymentStatus::Unpaid->value;
            return $this->repository->create($data);
        });
    }
}