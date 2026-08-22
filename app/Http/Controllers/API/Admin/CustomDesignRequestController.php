<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomDesignRequestRequest;
use App\Http\Requests\Admin\UpdateCustomDesignRequestRequest;
use App\Http\Resources\CustomDesignRequestResource;
use App\Models\CustomDesignRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomDesignRequestController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', CustomDesignRequest::class);

        $query = CustomDesignRequest::query()->with('customer:id,full_name');

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($customer) => $customer->where('full_name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate((int) $request->query('per_page', 15));

        return CustomDesignRequestResource::collection($requests);
    }

    public function store(StoreCustomDesignRequestRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['status'] = $data['status'] ?? 'new';
        $item = CustomDesignRequest::create($data)->loadMissing('customer:id,full_name');

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء طلب التصميم بنجاح.',
            'data' => new CustomDesignRequestResource($item),
        ], 201);
    }

    public function show(CustomDesignRequest $customDesignRequest): JsonResponse
    {
        $this->authorize('view', $customDesignRequest);

        return response()->json([
            'success' => true,
            'data' => new CustomDesignRequestResource($customDesignRequest->loadMissing('customer:id,full_name')),
        ]);
    }

    public function update(
        UpdateCustomDesignRequestRequest $request,
        CustomDesignRequest $customDesignRequest
    ): JsonResponse {
        $this->authorize('update', $customDesignRequest);

        $customDesignRequest->update($request->validated());
        $customDesignRequest->loadMissing('customer:id,full_name');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث طلب التصميم بنجاح.',
            'data' => new CustomDesignRequestResource($customDesignRequest),
        ]);
    }

    public function destroy(CustomDesignRequest $customDesignRequest): JsonResponse
    {
        $this->authorize('delete', $customDesignRequest);

        $customDesignRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف طلب التصميم بنجاح.',
        ]);
    }
}
