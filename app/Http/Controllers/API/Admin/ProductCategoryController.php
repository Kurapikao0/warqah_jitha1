<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductCategory\StoreProductCategoryRequest;
use App\Http\Requests\Admin\ProductCategory\UpdateProductCategoryRequest;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use App\Services\ProductCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;



class ProductCategoryController extends Controller
{

    public function __construct(
        protected ProductCategoryService $service
    ){
    }

    protected function normalizePayload(array $data, Request $request): array
    {
        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('product-categories', 'public');
        }

        unset($data['image']);

        return $data;
    }

    public function index(): AnonymousResourceCollection
    {

        return ProductCategoryResource::collection(
            $this->service->paginate()
        );
    }


    public function store(
        StoreProductCategoryRequest $request
    ): JsonResponse {

        $category =
            $this->service->store(
                $this->normalizePayload($request->validated(), $request)
            );


        return response()->json([

            'success'=>true,

            'data'=>new ProductCategoryResource(
                $category
            )

        ],201);
    }



    public function show(
        ProductCategory $category
    ){

        return new ProductCategoryResource(
            $category
        );
    }



    public function update(
        UpdateProductCategoryRequest $request,
        ProductCategory $category
    ){

        $updatedCategory =
            $this->service->update(
                $category,
                $this->normalizePayload($request->validated(), $request)
            );


        return new ProductCategoryResource(
            $updatedCategory
        );
    }



    public function destroy(
        ProductCategory $category
    ){

        $this->service->delete(
            $category
        );


        return response()->json([

            'success'=>true,

            'message'=>'Category deleted successfully.'

        ]);
    }
}
