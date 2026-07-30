<?php
use App\Http\Controllers\API\Admin\PermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Admin\ProductCustomizationController as AdminCustomization;
use App\Http\Controllers\API\Customer\ProductCustomizationController as CustomerCustomization;
use App\Http\Controllers\API\Admin\ProductController;
use App\Http\Controllers\API\Admin\OrderController as AdminOrder;
use App\Http\Controllers\API\Admin\OrderStatusController;
use App\Http\Controllers\API\Customer\CartController;
use App\Http\Controllers\API\Customer\CartItemController;
use App\Http\Controllers\API\Customer\FavoriteController;
use App\Http\Controllers\API\Admin\RoleController;
use App\Http\Controllers\API\Admin\RolePermissionController;
use App\Http\Controllers\API\Admin\AdminUserController;
use App\Http\Controllers\API\Admin\AdminPasswordResetController;
use App\Http\Controllers\API\Admin\AdminNotificationController;
use App\Http\Controllers\API\Admin\ActivityLogController;
use App\Http\Controllers\API\Admin\ProductCategoryController;
use App\Http\Controllers\API\Customer\AddressController;
use App\Http\Controllers\API\Customer\VerificationController;
use App\Http\Controllers\API\Admin\RawMaterialController;
use App\Http\Controllers\API\Customer\ReviewController;
use App\Http\Controllers\API\Customer\ReviewImageController;
use App\Http\Controllers\API\Customer\CustomerNotificationController;

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
'roles', 
RoleController::class);

Route::apiResource(
'products',
ProductController::class
);
Route::apiResource(
    'permissions',
    PermissionController::class
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

Route::get(
    'roles/{role}/permissions',
    [RolePermissionController::class,'index']
);


Route::post(
    'roles/{role}/permissions',
    [RolePermissionController::class,'store']
);


Route::delete(
    'roles/{role}/permissions/{permission}',
    [RolePermissionController::class,'destroy']
);

Route::apiResource(
    'admin-users',
    AdminUserController::class
);

Route::post(
    'admin-users/{adminUser}/password-reset',
    [AdminPasswordResetController::class,'store']
);


Route::delete(
    'password-resets/{reset}',
    [AdminPasswordResetController::class,'destroy']
);

Route::get(
    'admin-users/{adminUser}/notifications',
    [AdminNotificationController::class,'index']
);


Route::put(
    'notifications/{notification}/read',
    [AdminNotificationController::class,'read']
);

Route::apiResource(
    'activity-logs',
    ActivityLogController::class
)
->only([
    'index',
    'show'
]);

Route::apiResource(
    'product-categories',
    ProductCategoryController::class
);

Route::apiResource(
    'raw-materials',
    RawMaterialController::class
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

    Route::apiResource(
    'addresses',
    AddressController::class
    );

    Route::put(
    'addresses/{address}/default',
    [AddressController::class, 'setDefault']
);

    Route::post(
    'verifications/generate',
    [
        VerificationController::class,
        'generate'
    ]
);


Route::post(
    'verifications/verify',
    [
        VerificationController::class,
        'verify'
    ]
);

Route::apiResource(
    'reviews',
    ReviewController::class
); 

Route::post(
    'reviews/{review}/images',
    [ReviewImageController::class, 'store']
);

Route::delete(
    'review-images/{reviewImage}',
    [ReviewImageController::class, 'destroy']
); 

Route::get(
            'notifications',
            [CustomerNotificationController::class,'index']
        )
        ->name('notifications.index');


        Route::get(
            'notifications/{id}',
            [CustomerNotificationController::class,'show']
        )
        ->name('notifications.show');


        Route::patch(
            'notifications/{notification}/read',
            [CustomerNotificationController::class,'read']
        )
        ->name('notifications.read');


        Route::delete(
            'notifications/{notification}',
            [CustomerNotificationController::class,'destroy']
        )
        ->name('notifications.destroy');
});
