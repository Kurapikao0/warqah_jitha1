<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\ProductMedia;
use App\Services\ProductMediaService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductMediaResource;
use App\Http\Requests\ProductMedia\StoreProductMediaRequest;
use App\Http\Requests\ProductMedia\UpdateProductMediaRequest;
use App\Http\Requests\ProductMedia\UploadProductMediaRequest;
use App\Http\Requests\ProductMedia\ReorderProductMediaRequest;
use App\Http\Requests\ProductMedia\SetPrimaryProductMediaRequest;
use Illuminate\Http\JsonResponse;

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
    ): JsonResponse {
        $this->authorize('delete', $productMedia);

        $this->service->delete($productMedia);

        return response()->json([
            'message' => 'Media deleted successfully.',
        ]);
    }

    /**
     * Upload one or more media files for a product.
     * POST /admin/product-media/upload
     */
    public function upload(UploadProductMediaRequest $request): JsonResponse
    {
        $this->authorize('create', ProductMedia::class);

        $mediaItems = $this->service->upload(
            (int) $request->input('product_id'),
            $request->file('media', [])
        );

        return response()->json([
            'message' => 'Media uploaded successfully.',
            'data'    => ProductMediaResource::collection(collect($mediaItems)),
        ], 201);
    }

    /**
     * Reorder media items for a product.
     * PUT /admin/product-media/reorder
     */
    public function reorder(ReorderProductMediaRequest $request): JsonResponse
    {
        $productId  = (int) $request->input('product_id');
        $orderedIds = $request->input('orderedIds', $request->input('ordered_ids', []));

        $this->service->reorder($productId, $orderedIds);

        return response()->json([
            'message' => 'Media reordered successfully.',
        ]);
    }

    /**
     * Set a media item as primary for its product.
     * PUT /admin/product-media/{productMedia}/primary
     */
    public function setPrimary(
        SetPrimaryProductMediaRequest $request,
        ProductMedia $productMedia
    ): JsonResponse {
        $this->authorize('update', $productMedia);

        $this->service->setPrimary(
            (int) $productMedia->product_id,
            (int) $productMedia->id
        );

        return response()->json([
            'message' => 'Primary media updated successfully.',
            'data'    => new ProductMediaResource($productMedia->fresh()),
        ]);
    }
}