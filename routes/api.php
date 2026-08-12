<?php


use App\Http\Controllers\API\Admin\PermissionController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\Admin\ProductCustomizationController as AdminCustomization;
use App\Http\Controllers\API\Customer\ProductCustomizationController as CustomerCustomization;

use App\Http\Controllers\API\Admin\ProductController;
use App\Http\Controllers\API\Admin\OrderController as AdminOrder;
use App\Http\Controllers\API\Admin\OrderStatusController;
use App\Http\Controllers\API\Admin\OrderProductionController;
use App\Http\Controllers\API\Admin\OrderProductionStageController;

use App\Http\Controllers\API\Customer\OrderController as CustomerOrder;
use App\Http\Controllers\API\Customer\CartController;
use App\Http\Controllers\API\Customer\CartItemController;
use App\Http\Controllers\API\Customer\FavoriteController;

use App\Http\Controllers\API\Admin\RoleController;
use App\Http\Controllers\API\Admin\RolePermissionController;
use App\Http\Controllers\API\Admin\AdminUserController;
use App\Http\Controllers\API\Admin\CustomerController;
use App\Http\Controllers\API\Admin\AdminPasswordResetController;
use App\Http\Controllers\API\Admin\AdminNotificationController;
use App\Http\Controllers\API\Admin\ActivityLogController;
use App\Http\Controllers\API\Admin\ProductCategoryController;

use App\Http\Controllers\API\Customer\AddressController;
use App\Http\Controllers\API\Customer\VerificationController;
use App\Http\Controllers\API\Customer\ProfileController;

use App\Http\Controllers\API\Admin\RawMaterialController;

use App\Http\Controllers\API\Customer\ReviewController;
use App\Http\Controllers\API\Customer\ReviewImageController;
use App\Http\Controllers\API\Customer\CustomerNotificationController;

use App\Http\Controllers\API\Admin\PaymentController as AdminPayment;
use App\Http\Controllers\API\Customer\PaymentController as CustomerPayment;

use App\Http\Controllers\API\Admin\OrderStatusHistoryController;
use App\Http\Controllers\API\Admin\DesignPatternController;
use App\Http\Controllers\API\Admin\ProductMediaController;
use App\Http\Controllers\API\Admin\ColorController;
use App\Http\Controllers\API\Admin\ProductAttributeController;
use App\Http\Controllers\API\Admin\ProductAttributeValueController;

use App\Http\Controllers\API\Customer\AuthController;
use App\Http\Controllers\API\Admin\AuthController as AdminAuth;
use App\Http\Controllers\API\Admin\ReviewController as AdminReviewController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/test', function () {

    return response()->json([
        'status' => true,
        'message' => 'API is working'
    ]);
});


Route::post(
    'customer/verifications/generate',
    [VerificationController::class, 'generate']
);

Route::post(
    'customer/verifications/verify',
    [VerificationController::class, 'verify']
);


/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

    Route::post(
        'login',
        [AdminAuth::class, 'login']
    );
});


/*
|--------------------------------------------------------------------------
| Customer Authentication
|--------------------------------------------------------------------------
*/

Route::post(
    'register',
    [AuthController::class, 'register']
);

Route::post(
    'login',
    [AuthController::class, 'login']
);


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth:sanctum'])
    ->group(function () {

        Route::apiResource(
            'roles',
            RoleController::class
        );

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
            [AdminCustomization::class, 'index']
        );

        Route::get(
            'customizations/{id}',
            [AdminCustomization::class, 'show']
        );

        Route::put(
            'customizations/{customization}/status',
            [AdminCustomization::class, 'updateStatus']
        );


        Route::apiResource(
            'orders',
            AdminOrder::class
        );

        Route::put(
            'orders/{order}/status',
            [OrderStatusController::class, 'update']
        );

        Route::get(
            'orders-statistics',
            [AdminOrder::class, 'statistics']
        );
        Route::get(
            'orders/{order}/production-history',
            [OrderProductionController::class, 'history']
        );

        Route::post(
            'orders/{order}/next-stage',
            [OrderProductionController::class, 'changeStage']
        );

        Route::post(
            'orders/{order}/stage/{stageId}',
            [OrderProductionController::class, 'updateStage']
        );

        Route::get(
            'orders/{order}/status-history',
            [OrderStatusHistoryController::class, 'index']
        );


        Route::get(
            'payments',
            [AdminPayment::class, 'index']
        );

        Route::get(
            'payments/{id}',
            [AdminPayment::class, 'show']
        );

        Route::put(
            'payments/{payment}/status',
            [AdminPayment::class, 'updateStatus']
        );


        Route::apiResource(
            'production-stages',
            OrderProductionStageController::class
        );

        Route::apiResource(
            'design-patterns',
            DesignPatternController::class
        );

        Route::apiResource(
            'product-media',
            ProductMediaController::class
        );

        Route::apiResource(
            'colors',
            ColorController::class
        );

        Route::apiResource(
            'product-attributes',
            ProductAttributeController::class
        );

        Route::apiResource(
            'product-attribute-values',
            ProductAttributeValueController::class
        );


        Route::post(
            'roles/{role}/permissions',
            [RolePermissionController::class, 'store']
        );

        Route::delete(
            'roles/{role}/permissions/{permission}',
            [RolePermissionController::class, 'destroy']
        );

        Route::get(
            'roles/{role}/permissions',
            [RolePermissionController::class, 'index']
        );


        Route::apiResource(
            'admin-users',
            AdminUserController::class
        );


        Route::post(
            'admin-users/{adminUser}/password-reset',
            [AdminPasswordResetController::class, 'store']
        );

        Route::delete(
            'password-resets/{reset}',
            [AdminPasswordResetController::class, 'destroy']
        );


        Route::get(
            'admin-users/{adminUser}/notifications',
            [AdminNotificationController::class, 'index']
        );

        Route::put(
            'notifications/{notification}/read',
            [AdminNotificationController::class, 'read']
        );


        /*
        |--------------------------------------------------------------------------
        | Customer Management (Admin)
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'customers',
            CustomerController::class
        )->except([
            'create',
            'edit'
        ]);


        Route::patch(
            'customers/{customer}/restore',
            [CustomerController::class, 'restore']
        );

        Route::patch(
            'customers/{customer}/status',
            [CustomerController::class, 'changeStatus']
        );

        Route::patch(
            'customers/{customer}/verify',
            [CustomerController::class, 'verify']
        );


        Route::get(
            'reviews',
            [AdminReviewController::class, 'index']
        );

        Route::put(
            'reviews/{review}/status',
            [AdminReviewController::class, 'updateStatus']
        );

        Route::post(
            'reviews/{review}/reply',
            [AdminReviewController::class, 'reply']
        );


        Route::apiResource(
            'activity-logs',
            ActivityLogController::class
        )->only([
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


        Route::post(
            'logout',
            [AdminAuth::class, 'logout']
        );
    });
/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/

Route::prefix('customer')
    ->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Customer Authentication
        |--------------------------------------------------------------------------
        */

        Route::post(
            'login',
            [AuthController::class, 'login']
        );


        Route::post(
            'register',
            [AuthController::class, 'register']
        );


        Route::post(
            'logout',
            [AuthController::class, 'logout']
        )
            ->middleware('auth:sanctum');
    });



Route::prefix('customer')
    ->middleware('auth:sanctum')
    ->group(function () {


        Route::apiResource(
            'customizations',
            CustomerCustomization::class
        );


        Route::apiResource(
            'orders',
            CustomerOrder::class
        )
            ->except([
                'update',
                'destroy'
            ]);


        Route::get(
            'cart',
            [CartController::class, 'index']
        );


        Route::post(
            'cart/items',
            [CartItemController::class, 'store']
        );


        Route::put(
            'cart/items/{cartItem}',
            [CartItemController::class, 'update']
        );


        Route::delete(
            'cart/items/{cartItem}',
            [CartItemController::class, 'destroy']
        );


        Route::get(
            'favorites',
            [FavoriteController::class, 'index']
        );


        Route::post(
            'favorites/{product}',
            [FavoriteController::class, 'toggle']
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
            [VerificationController::class, 'generate']
        );


        Route::post(
            'verifications/verify',
            [VerificationController::class, 'verify']
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
            [CustomerNotificationController::class, 'index']
        )
            ->name('notifications.index');


        Route::get(
            'notifications/{id}',
            [CustomerNotificationController::class, 'show']
        )
            ->name('notifications.show');


        Route::patch(
            'notifications/{notification}/read',
            [CustomerNotificationController::class, 'read']
        )
            ->name('notifications.read');


        Route::delete(
            'notifications/{notification}',
            [CustomerNotificationController::class, 'destroy']
        )
            ->name('notifications.destroy');


        Route::apiResource(
            'payments',
            CustomerPayment::class
        )
            ->only([
                'index',
                'store',
                'show'
            ]);


        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        Route::get(
            'profile',
            [ProfileController::class, 'show']
        );


        Route::put(
            'profile',
            [ProfileController::class, 'update']
        );


        Route::put(
            'profile/password',
            [ProfileController::class, 'updatePassword']
        );


        Route::post(
            'profile/avatar',
            [ProfileController::class, 'updateAvatar']
        );
    });
