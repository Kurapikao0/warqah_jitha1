<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\OrderProductionStage;
use App\Services\OrderProductionStageService;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderProductionStageResource;
use App\Http\Requests\OrderProductionStage\StoreOrderProductionStageRequest;
use App\Http\Requests\OrderProductionStage\UpdateOrderProductionStageRequest;

class OrderProductionStageController extends Controller
{
    public function __construct(
        protected OrderProductionStageService $service
    ) {
    }

    public function index()
    {
        return OrderProductionStageResource::collection(
            $this->service->all()
        );
    }

    public function store(
        StoreOrderProductionStageRequest $request
    ) {
        $stage = $this->service->create(
            $request->validated()
        );

        return new OrderProductionStageResource($stage);
    }

    public function show(
        OrderProductionStage $orderProductionStage
    ) {
        return new OrderProductionStageResource(
            $orderProductionStage
        );
    }

    public function update(
        UpdateOrderProductionStageRequest $request,
        OrderProductionStage $orderProductionStage
    ) {
        $this->service->update(
            $orderProductionStage,
            $request->validated()
        );

        return new OrderProductionStageResource(
            $orderProductionStage->refresh()
        );
    }

    public function destroy(
        OrderProductionStage $orderProductionStage
    ) {
        $this->service->delete($orderProductionStage);

        return response()->json([
            'message' => 'Stage deleted successfully.'
        ]);
    }
}