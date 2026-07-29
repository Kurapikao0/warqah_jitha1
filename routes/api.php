<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Admin\ProductCustomizationController as AdminCustomization;
use App\Http\Controllers\API\Customer\ProductCustomizationController as CustomerCustomization;
use App\Http\Controllers\API\Admin\ProductController;
use App\Http\Controllers\API\Admin\OrderController as AdminOrder;
use App\Http\Controllers\API\Admin\OrderStatusController;
use App\Http\Controllers\API\Customer\CartController;
use App\Http\Controllers\API\Customer\CartItemController;
use App\Http\Controllers\API\Customer\FavoriteController;

Route::get('/test', function () {

    return response()->json([
        'status' => true,
        'message' => 'API is working'
    ]);

});

Route::prefix('admin')
->middleware(['auth:sanctum'])
->group(function(){


Route::apiResource(
'products',
ProductController::class
);

Route::get(
'customizations',
[AdminCustomization::class,'index']
);


Route::get(
'customizations/{id}',
[AdminCustomization::class,'show']
);


Route::put(
'customizations/{customization}/status',
[AdminCustomization::class,'updateStatus']
);

Route::apiResource(
'orders',
AdminOrder::class
);


Route::put(
'orders/{order}/status',
[OrderStatusController::class,'update']
);



});


Route::prefix('customer')
->middleware('auth:sanctum')
->group(function(){


Route::apiResource(
'customizations',
CustomerCustomization::class
);

Route::apiResource(
'orders',
\App\Http\Controllers\API\Customer\OrderController::class
);

Route::get(
'cart',
[CartController::class,'index']
);



Route::post(
'cart/items',
[CartItemController::class,'store']
);



Route::put(
'cart/items/{cartItem}',
[CartItemController::class,'update']
);



Route::delete(
'cart/items/{cartItem}',
[CartItemController::class,'destroy']
);


Route::get(
'favorites',
[FavoriteController::class,'index']
);


Route::post(
'favorites/{product}',
[FavoriteController::class,'toggle']
);

});
