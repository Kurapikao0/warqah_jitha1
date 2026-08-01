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
        OrderProductionStage $productionStage
    ) {
        return new OrderProductionStageResource(
            $productionStage
        );
    }

    public function update(
        UpdateOrderProductionStageRequest $request,
        OrderProductionStage $productionStage
    ) {
        /*dd(
        $productionStage,
        $request->validated()
    );*/
        $this->service->update(
            $productionStage,
            $request->validated()
        );

        return new OrderProductionStageResource(
            $productionStage->refresh()
        );
    }

    public function destroy(
        OrderProductionStage $productionStage
    ) {
        $this->service->delete($productionStage);

        return response()->json([
            'message' => 'Stage deleted successfully.'
        ]);
    }
}