<?php

namespace App\Http\Controllers\API\Admin;


use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;



class ProductController extends Controller
{


public function __construct(
    protected ProductService $service
)
{}





public function index()
{

return ProductResource::collection(
    $this->service->getAll()
);

}





public function store(StoreProductRequest $request)
{


$product =
$this->service->create(
    $request->validated()
);



return new ProductResource($product);


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
)
{


$this->service->update(
    $product,
    $request->validated()
);


return response()->json([

'message'=>'Product updated successfully'

]);


}






public function destroy(Product $product)
{

$this->service->delete($product);


return response()->json([

'message'=>'Product deleted successfully'

]);


}



}