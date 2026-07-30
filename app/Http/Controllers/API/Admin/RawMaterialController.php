<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RawMaterial\StoreRawMaterialRequest;
use App\Http\Requests\RawMaterial\UpdateRawMaterialRequest;
use App\Http\Resources\RawMaterialResource;
use App\Models\RawMaterial;
use App\Services\RawMaterialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
{
    public function __construct(
        protected RawMaterialService $service
    ) {
    }


    /**
     * Display all raw materials
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => RawMaterialResource::collection(
                $this->service->getAll()
            )
        ]);
    }


    /**
     * Store raw material
     */
    public function store(
        StoreRawMaterialRequest $request
    ): JsonResponse {

        $rawMaterial = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Raw material created successfully',
            'data' => new RawMaterialResource($rawMaterial)
        ], 201);
    }


    /**
     * Show raw material
     */
    public function show(
        RawMaterial $rawMaterial
    ): JsonResponse {

        return response()->json([
            'data' => new RawMaterialResource(
                $rawMaterial->load('product')
            )
        ]);
    }


    /**
     * Update raw material
     */
    public function update(
        UpdateRawMaterialRequest $request,
        RawMaterial $rawMaterial
    ): JsonResponse {

        $rawMaterial = $this->service->update(
            $rawMaterial,
            $request->validated()
        );


        return response()->json([
            'message' => 'Raw material updated successfully',
            'data' => new RawMaterialResource($rawMaterial)
        ]);
    }


    /**
     * Delete raw material
     */
    public function destroy(
        RawMaterial $rawMaterial
    ): JsonResponse {

        $this->service->delete($rawMaterial);

        return response()->json([
            'message' => 'Raw material deleted successfully'
        ]);
    }
}