<?php

namespace App\Http\Controllers\API\Admin;


use App\Models\Payment;
use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Http\Resources\PaymentResource;
use App\Http\Requests\Payment\UpdatePaymentStatusRequest;



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

$this->service->all()

);


}




public function show($id)
{


return new PaymentResource(

$this->service->find($id)

);


}





public function updateStatus(
UpdatePaymentStatusRequest $request,
Payment $payment
)
{


$this->service
->updateStatus(
$payment,
$request->validated()
);



return response()->json([

'message'=>
'Payment status updated successfully'

]);


}



}