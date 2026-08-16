<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RawMaterial\StoreRawMaterialRequest;
use App\Http\Requests\RawMaterial\UpdateRawMaterialRequest;
use App\Http\Resources\RawMaterialResource;
use App\Models\RawMaterial;
use App\Services\RawMaterialService;
use Illuminate\Http\JsonResponse;

class RawMaterialController extends Controller
{
    public function __construct(
        protected RawMaterialService $service
    ) {}

    /**
     * عرض قائمة المواد الخام
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', RawMaterial::class);

        $rawMaterials = $this->service->getAll();

        return RawMaterialResource::collection($rawMaterials)
            ->additional(['success' => true])
            ->response();
    }

    /**
     * إنشاء مادة خام جديدة
     */
    public function store(StoreRawMaterialRequest $request): JsonResponse
    {
        $this->authorize('create', RawMaterial::class);

        $rawMaterial = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المادة الخام بنجاح',
            'data' => new RawMaterialResource($rawMaterial),
        ], 201);
    }

    /**
     * عرض تفاصيل مادة خام محددة
     */
    public function show(RawMaterial $rawMaterial): JsonResponse
    {
        $this->authorize('view', $rawMaterial);

        return response()->json([
            'success' => true,
            'data' => new RawMaterialResource($rawMaterial->loadMissing('product')),
        ]);
    }

    /**
     * تحديث بيانات مادة خام
     */
    public function update(
        UpdateRawMaterialRequest $request,
        RawMaterial $rawMaterial
    ): JsonResponse {
        $this->authorize('update', $rawMaterial);

        $updatedRawMaterial = $this->service->update(
            $rawMaterial,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المادة الخام بنجاح',
            'data' => new RawMaterialResource($updatedRawMaterial),
        ]);
    }

    /**
     * حذف مادة خام
     */
    public function destroy(RawMaterial $rawMaterial): JsonResponse
    {
        $this->authorize('delete', $rawMaterial);

        $this->service->delete($rawMaterial);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المادة الخام بنجاح',
        ]);
    }
}
