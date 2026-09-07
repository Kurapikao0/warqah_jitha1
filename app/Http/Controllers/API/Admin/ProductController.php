<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function index(Request $request)
    {
        return ProductResource::collection(
            $this->service->getAll(
                $request->query('search'),
                (int) $request->query('per_page', 20)
            )
        );
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Product created successfully',
            'data' => new ProductResource(
                $product->load([
                    'category',
                    'media',
                    'attributes',
                ])
            ),
        ], 201);
    }

    public function show($id)
    {

        return new ProductResource(
            $this->service->getById($id)
        );

    }

    public function update(
        UpdateProductRequest $request,
        Product $product
    ) {
        $this->service->update(
            $product,
            $request->validated()
        );

        $product = $product->fresh([
            'category',
            'media',
            'attributes',
        ]);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product),
        ]);
    }

    public function destroy(Product $product)
    {

        $this->service->delete($product);

        return response()->json([

            'message' => 'Product deleted successfully',

        ]);

    }
}
