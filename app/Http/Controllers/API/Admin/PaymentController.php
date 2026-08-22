<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\UpdatePaymentStatusRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $service
    ) {}

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

    public function destroy(Payment $payment)
    {
        $this->service->delete($payment);

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully',
        ]);
    }

    public function updateStatus(
        UpdatePaymentStatusRequest $request,
        Payment $payment
    ) {

        $this->service
            ->updateStatus(
                $payment,
                $request->validated()
            );

        return response()->json([

            'message' => 'Payment status updated successfully',

        ]);

    }
}
