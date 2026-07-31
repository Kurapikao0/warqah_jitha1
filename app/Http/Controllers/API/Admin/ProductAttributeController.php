<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\ProductAttribute;
use App\Services\ProductAttributeService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductAttributeResource;
use App\Http\Requests\ProductAttribute\StoreProductAttributeRequest;
use App\Http\Requests\ProductAttribute\UpdateProductAttributeRequest;

class ProductAttributeController extends Controller
{
    public function __construct(
        protected ProductAttributeService $service
    ) {
    }

    public function index()
    {
        $this->authorize('viewAny', ProductAttribute::class);

        return ProductAttributeResource::collection(
            $this->service->all()
        );
    }

    public function store(
        StoreProductAttributeRequest $request
    ) {
        $this->authorize('create', ProductAttribute::class);

        $attribute = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Product attribute created successfully.',
            'data' => new ProductAttributeResource($attribute),
        ], 201);
    }

    public function show(
        ProductAttribute $productAttribute
    ) {
        $this->authorize('view', $productAttribute);

        return new ProductAttributeResource(
            $productAttribute->load('values')
        );
    }

    public function update(
        UpdateProductAttributeRequest $request,
        ProductAttribute $productAttribute
    ) {
        $this->authorize('update', $productAttribute);

        $this->service->update(
            $productAttribute,
            $request->validated()
        );

        return response()->json([
            'message' => 'Product attribute updated successfully.',
            'data' => new ProductAttributeResource(
                $productAttribute->fresh()->load('values')
            ),
        ]);
    }

    public function destroy(
        ProductAttribute $productAttribute
    ) {
        $this->authorize('delete', $productAttribute);

        $this->service->delete($productAttribute);

        return response()->json([
            'message' => 'Product attribute deleted successfully.',
        ]);
    }
}