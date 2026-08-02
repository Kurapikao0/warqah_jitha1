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


class ProductCategoryController extends Controller
{

    public function __construct(
        protected ProductCategoryService $service
    ){
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
                $request->validated()
            );


        return response()->json([

            'success'=>true,

            'data'=>new ProductCategoryResource(
                $category
            )

        ],201);
    }



    public function show(
        ProductCategory $productCategory
    ){

        return new ProductCategoryResource(
            $productCategory
        );
    }



    public function update(
        UpdateProductCategoryRequest $request,
        ProductCategory $productCategory
    ){

        $category =
            $this->service->update(
                $productCategory,
                $request->validated()
            );


        return new ProductCategoryResource(
            $category
        );
    }



    public function destroy(
        ProductCategory $productCategory
    ){

        $this->service->delete(
            $productCategory
        );


        return response()->json([

            'success'=>true,

            'message'=>'Category deleted successfully.'

        ]);
    }
}