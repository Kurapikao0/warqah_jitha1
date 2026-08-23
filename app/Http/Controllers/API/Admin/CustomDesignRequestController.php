<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomDesignRequestRequest;
use App\Http\Requests\Admin\UpdateCustomDesignRequestRequest;
use App\Http\Resources\CustomDesignRequestResource;
use App\Models\CustomDesignRequest;
use App\Services\CustomDesignRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\CustomDesignRequestImage;

class CustomDesignRequestController extends Controller
{
    public function __construct(
        protected CustomDesignRequestService $service
    ) {}

    public function index(Request $request)
    {
        $this->authorize(
            'viewAny',
            CustomDesignRequest::class
        );

        $requests = $this->service->getAll([
            'search' => $request->query('search'),
            'status' => $request->query('status'),
            'per_page' => $request->query('per_page', 15),
        ]);

        return CustomDesignRequestResource::collection($requests);
    }

    public function store(
        StoreCustomDesignRequestRequest $request
    ): JsonResponse {
        $this->authorize(
            'create',
            CustomDesignRequest::class
        );

        $data = $request->validated();

        $files = $request->file('images', []);

        unset($data['images']);

        $item = $this->service->create(
            $data,
            $files
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء طلب التصميم بنجاح.',
            'data' => new CustomDesignRequestResource($item),
        ], 201);
    }

    public function show(
        CustomDesignRequest $customDesignRequest
    ): JsonResponse {
        $this->authorize(
            'view',
            $customDesignRequest
        );

        return response()->json([
            'success' => true,
            'data' => new CustomDesignRequestResource(
                $this->service->find($customDesignRequest->id)
            ),
        ]);
    }

    public function update(
        UpdateCustomDesignRequestRequest $request,
        CustomDesignRequest $customDesignRequest
    ): JsonResponse {
        $this->authorize(
            'update',
            $customDesignRequest
        );

        $data = $request->validated();

        $files = $request->file('images', []);

        unset($data['images']);

        $item = $this->service->update(
            $customDesignRequest,
            $data,
            $files
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث طلب التصميم بنجاح.',
            'data' => new CustomDesignRequestResource($item),
        ]);
    }

    public function destroyImage(
        CustomDesignRequest $customDesignRequest,
        CustomDesignRequestImage $image
    ): JsonResponse {
        $this->authorize(
            'delete',
            $customDesignRequest
        );

        abort_unless(
            $image->custom_design_request_id === $customDesignRequest->id,
            404
        );

        $this->service->deleteImage($image);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الصورة بنجاح.',
        ]);
    }
    public function destroy(
        CustomDesignRequest $customDesignRequest
    ): JsonResponse {
        $this->authorize(
            'delete',
            $customDesignRequest
        );

        $this->service->delete($customDesignRequest);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف طلب التصميم بنجاح.',
        ]);
    }
}
