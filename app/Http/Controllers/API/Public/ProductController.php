<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Public;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Retrieve a paginated list of active products.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 20);
        $categoryId = $request->query('category_id');

        $query = Product::with(['category', 'media', 'attributes'])
            ->where('status', ProductStatus::Active->value);

        if (filled($search)) {
            $query->where(function ($q) use ($search) {
                // Using ILIKE for Postgres or standard LIKE for MySQL. Laravel maps 'ilike' fallback safely in most drivers, but standard 'like' is universally safer if db engine isn't strictly pg.
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        if (filled($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        return ProductResource::collection(
            $query->latest()->paginate($perPage)->withQueryString()
        );
    }

    /**
     * Retrieve a specific active product by ID.
     */
    public function show(int $id): ProductResource
    {
        $product = Product::with(['category', 'media', 'colors', 'attributes'])
            ->where('status', ProductStatus::Active->value)
            ->findOrFail($id);

        return new ProductResource($product);
    }

    /**
     * Return the min and max prices of active products for filtering.
     */
    public function priceRange()
    {
        $min = Product::where('status', ProductStatus::Active->value)->min('price') ?? 0;
        $max = Product::where('status', ProductStatus::Active->value)->max('price') ?? 5000;

        return response()->json([
            'min_price' => (float) $min,
            'max_price' => (float) $max,
        ]);
    }
}
