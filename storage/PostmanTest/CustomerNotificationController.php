<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerNotificationResource;
use App\Models\CustomerNotification;
use App\Services\CustomerNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerNotificationController extends Controller
{

    public function __construct(
        protected CustomerNotificationService $service
    ) {
    }


    public function index(
        Request $request
    ): JsonResponse {

        $customer = $request->user();


        return response()->json([
            'data' => CustomerNotificationResource::collection(
                $this->service->getAll(
                    $customer->id
                )
            )
        ]);
    }



    public function show(
        Request $request,
        int $id
    ): JsonResponse {


        $notification =
            $this->service->getById(
                $id,
                $request->user()->id
            );


        return response()->json([
            'data' =>
                new CustomerNotificationResource(
                    $notification
                )
        ]);
    }



    public function read(
        Request $request,
        CustomerNotification $notification
    ): JsonResponse {


        abort_if(
            $notification->customer_id !== $request->user()->id,
            403
        );


        return response()->json([
            'message' =>
                'Notification marked as read',

            'data' =>
                new CustomerNotificationResource(
                    $this->service->markAsRead(
                        $notification
                    )
                )
        ]);
    }



    public function destroy(
        Request $request,
        CustomerNotification $notification
    ): JsonResponse {


        abort_if(
            $notification->customer_id !== $request->user()->id,
            403
        );


        $this->service->delete(
            $notification
        );


        return response()->json([
            'message' =>
                'Notification deleted successfully'
        ]);
    }
}