<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;

class CategoryCatalogController extends Controller
{
    public function __construct(protected ProductCategoryService $service) {}

    public function index(Request $request)
    {
        // Use service paginate to keep behavior consistent with admin
        $paginator = $this->service->paginate();

        return ProductCategoryResource::collection($paginator);
    }
}
