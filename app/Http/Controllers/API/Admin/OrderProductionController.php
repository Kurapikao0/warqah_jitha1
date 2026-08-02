<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Services\OrderProductionService;
use App\Http\Requests\OrderProduction\UpdateOrderStageRequest;

class OrderProductionController extends Controller
{

    public function __construct(
        protected OrderProductionService $service
    ) {
    }


    /**
     * Move order to next production stage
     */
    public function changeStage(Order $order)
    {
        $result = $this->service->changeStage($order);

        if (!$result) {
            return response()->json([
                'message' => 'No next production stage'
            ],404);
        }


        return response()->json([

            'message' => 'Order moved to next production stage',

            'stage' =>
                $result->currentProductionStage?->name

        ]);
    }



    /**
     * Set specific production stage
     */
    public function updateStage(
    UpdateOrderStageRequest $request,
    Order $order
){

        $this->service->updateStage(
        $order,
        $request->validated()['stage_id']
    );


        return response()->json([

            'message' =>
                'Production stage updated'

        ]);
    }



    /**
     * Production history
     */
    public function history(Order $order)
    {
        return response()->json(
            $this->service->history($order)
        );
    }

}