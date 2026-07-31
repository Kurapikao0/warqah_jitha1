<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\ProductMedia;
use App\Services\ProductMediaService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductMediaResource;
use App\Http\Requests\ProductMedia\StoreProductMediaRequest;
use App\Http\Requests\ProductMedia\UpdateProductMediaRequest;

class ProductMediaController extends Controller
{
    public function __construct(
        protected ProductMediaService $service
    ) {
    }

    public function index()
    {
        $this->authorize('viewAny', ProductMedia::class);

        return ProductMediaResource::collection(
            $this->service->all()
        );
    }

    public function store(
        StoreProductMediaRequest $request
    ) {
        $this->authorize('create', ProductMedia::class);

        $media = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Media created successfully.',
            'data' => new ProductMediaResource(
                $media->load('product')
            )
        ], 201);
    }

    public function show(
        ProductMedia $productMedia
    ) {
        $this->authorize('view', $productMedia);

        return new ProductMediaResource(
            $productMedia->load('product')
        );
    }

    public function update(
        UpdateProductMediaRequest $request,
        ProductMedia $productMedia
    ) {
        $this->authorize('update', $productMedia);

        $this->service->update(
            $productMedia,
            $request->validated()
        );

        return response()->json([
            'message' => 'Media updated successfully.',
            'data' => new ProductMediaResource(
                $productMedia->fresh()->load('product')
            )
        ]);
    }

    public function destroy(
        ProductMedia $productMedia
    ) {
        $this->authorize('delete', $productMedia);

        $this->service->delete($productMedia);

        return response()->json([
            'message' => 'Media deleted successfully.'
        ]);
    }
}