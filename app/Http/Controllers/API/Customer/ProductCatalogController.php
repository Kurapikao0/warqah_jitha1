<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductCatalogController extends Controller
{
    public function __construct(protected ProductService $service) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 9);
        $page = (int) $request->query('page', 1);
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        $paginator = $this->service->getAll($search, $perPage, $categoryId);

        // Return paginated resource collection (keeps meta and links via resource collection)
        return ProductResource::collection($paginator);
    }

    public function show($id)
    {
        $product = $this->service->getById($id);
        return new ProductResource($product);
    }
}
