<?php

namespace App\Http\Controllers\API\Customer;


use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Http\Resources\PaymentResource;
use App\Http\Requests\Payment\StorePaymentRequest;



class PaymentController extends Controller
{


public function __construct(
protected PaymentService $service
)
{

}





public function index()
{

return PaymentResource::collection(

$this->service
->customerPayments(
auth()->id()
)

);

}





    public function store(
    StorePaymentRequest $request
    )
    {
        $data=$request->validated();
        $payment =
            $this->service->create($data);
        $this->authorize('view', $payment);
        return new PaymentResource($payment);
    }





/*public function show($id)
{

return new PaymentResource(

$this->service->find($id)

);

}*/

public function show($id)
{
    $payment = $this->service
        ->findCustomerPayment(
            auth()->id(),
            $id
        );
    $this->authorize('view', $payment);
    return new PaymentResource($payment);
}

}