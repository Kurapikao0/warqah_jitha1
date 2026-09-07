<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Retrieve all categories for the public catalog interface.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = ProductCategory::all();
        
        return ProductCategoryResource::collection($categories);
    }
}
