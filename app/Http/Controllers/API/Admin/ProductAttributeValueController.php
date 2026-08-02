<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\ProductAttributeValue;
use App\Services\ProductAttributeValueService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductAttributeValueResource;
use App\Http\Requests\ProductAttributeValue\StoreProductAttributeValueRequest;
use App\Http\Requests\ProductAttributeValue\UpdateProductAttributeValueRequest;

class ProductAttributeValueController extends Controller
{
    public function __construct(
        protected ProductAttributeValueService $service
    ) {
    }

    public function index()
    {
        $this->authorize('viewAny', ProductAttributeValue::class);

        return ProductAttributeValueResource::collection(
            $this->service->all()
        );
    }

    public function store(
        StoreProductAttributeValueRequest $request
    ) {
        $this->authorize('create', ProductAttributeValue::class);

        $attributeValue = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Product attribute value created successfully.',
            'data' => new ProductAttributeValueResource(
                $attributeValue->load([
                    'product',
                    'attribute'
                ])
            ),
        ], 201);
    }

    public function show(
        ProductAttributeValue $productAttributeValue
    ) {
        $this->authorize('view', $productAttributeValue);

        return new ProductAttributeValueResource(
            $productAttributeValue->load([
                'product',
                'attribute'
            ])
        );
    }

    public function update(
        UpdateProductAttributeValueRequest $request,
        ProductAttributeValue $productAttributeValue
    ) {
        $this->authorize('update', $productAttributeValue);

        $this->service->update(
            $productAttributeValue,
            $request->validated()
        );

        return response()->json([
            'message' => 'Product attribute value updated successfully.',
            'data' => new ProductAttributeValueResource(
                $productAttributeValue
                    ->fresh()
                    ->load([
                        'product',
                        'attribute'
                    ])
            ),
        ]);
    }

    public function destroy(
        ProductAttributeValue $productAttributeValue
    ) {
        $this->authorize('delete', $productAttributeValue);

        $this->service->delete(
            $productAttributeValue
        );

        return response()->json([
            'message' => 'Product attribute value deleted successfully.',
        ]);
    }
}