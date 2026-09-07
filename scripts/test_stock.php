<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Customer;
use App\Models\CartItem;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

$product = Product::first();
echo "Product ID: " . $product->id . "\n";
echo "Stock: " . $product->stock_quantity . "\n";
echo "Reserved: " . $product->reserved_quantity . "\n";
echo "Available (Accessor): " . $product->available_stock . "\n";

$resource = new ProductResource($product);
$request = Request::create('/api/products/' . $product->id, 'GET');
$array = $resource->toArray($request);
echo "Available (Resource): " . $array['available_stock'] . "\n";
