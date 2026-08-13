<?php


use App\Http\Controllers\API\Admin\PermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\Auth\AdminAuthController;
use App\Http\Controllers\API\Auth\CustomerAuthController;

/*
|--------------------------------------------------------------------------
| Admin Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\Admin\ActivityLogController;
use App\Http\Controllers\API\Admin\AdminNotificationController;
use App\Http\Controllers\API\Admin\AdminPasswordResetController;
use App\Http\Controllers\API\Admin\AdminUserController;
use App\Http\Controllers\API\Admin\ColorController;
use App\Http\Controllers\API\Admin\CustomerController;
use App\Http\Controllers\API\Admin\DesignPatternController;
use App\Http\Controllers\API\Admin\OrderController as AdminOrder;
use App\Http\Controllers\API\Admin\OrderProductionController;
use App\Http\Controllers\API\Admin\OrderProductionStageController;
use App\Http\Controllers\API\Admin\OrderStatusController;
use App\Http\Controllers\API\Admin\OrderStatusHistoryController;
use App\Http\Controllers\API\Admin\PaymentController as AdminPayment;
use App\Http\Controllers\API\Admin\PermissionController;
use App\Http\Controllers\API\Admin\ProductAttributeController;
use App\Http\Controllers\API\Admin\ProductAttributeValueController;
use App\Http\Controllers\API\Admin\ProductCategoryController;
use App\Http\Controllers\API\Admin\ProductController;
use App\Http\Controllers\API\Admin\ProductCustomizationController as AdminCustomization;
use App\Http\Controllers\API\Admin\ProductMediaController;
use App\Http\Controllers\API\Admin\RawMaterialController;
use App\Http\Controllers\API\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\API\Admin\RoleController;
use App\Http\Controllers\API\Admin\RolePermissionController;

/*
|--------------------------------------------------------------------------
| Customer Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\Customer\PasswordResetController;
use App\Http\Controllers\API\Customer\AddressController;
use App\Http\Controllers\API\Customer\CartController;
use App\Http\Controllers\API\Customer\CartItemController;
use App\Http\Controllers\API\Customer\CustomerNotificationController;
use App\Http\Controllers\API\Customer\FavoriteController;
use App\Http\Controllers\API\Customer\OrderController as CustomerOrder;
use App\Http\Controllers\API\Customer\PaymentController as CustomerPayment;
use App\Http\Controllers\API\Customer\ProductCustomizationController as CustomerCustomization;
use App\Http\Controllers\API\Customer\ProfileController;
use App\Http\Controllers\API\Customer\ReviewController;
use App\Http\Controllers\API\Customer\ReviewImageController;
use App\Http\Controllers\API\Customer\VerificationController;

Route::get('/test-admin-auth', function () {
    return response()->json([
        'admin' => auth('admin')->user(),
        'sanctum' => auth()->user(),
    ]);
})->middleware('auth:admin');
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| API Health Check
|--------------------------------------------------------------------------
*/

Route::get('/test', function (): \Illuminate\Http\JsonResponse {
    return response()->json([
        'status' => true,
        'message' => 'API is working',
    ]);
});


/*
|--------------------------------------------------------------------------
| Customer Authentication - Legacy Aliases
|--------------------------------------------------------------------------
|
| Kept for backward compatibility with existing frontend clients.
|
*/

Route::post(
    'register',
    [CustomerAuthController::class, 'register']
);

Route::post(
    'login',
    [CustomerAuthController::class, 'login']
)->middleware('throttle:5,1');


/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
|
| Login is public.
| Logout requires an authenticated Admin Sanctum token.
|
*/

Route::prefix('admin')->group(function () {

    Route::post(
        'login',
        [AdminAuth::class, 'login']
    );
});


/*
|--------------------------------------------------------------------------
| Customer Verification - Public
|--------------------------------------------------------------------------
|
| These endpoints are intentionally public because verification can be
| required before the customer is fully authenticated.
|
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
| Admin API
|--------------------------------------------------------------------------
|
| Every route in this group requires an authenticated Admin.
|
| IMPORTANT:
| We explicitly use auth:admin instead of auth:sanctum.
|
*/

Route::prefix('admin')
    ->middleware(['auth:sanctum'])
    ->group(function () {

        Route::apiResource(
            'roles',
            RoleController::class
        );

        // Role Permissions
        Route::get(
            'roles/{role}/permissions',
            [RolePermissionController::class, 'index']
        )->name('roles.permissions.index');

        Route::post(
            'roles/{role}/permissions',
            [RolePermissionController::class, 'store']
        )->name('roles.permissions.store');

        Route::delete(
            'roles/{role}/permissions/{permission}',
            [RolePermissionController::class, 'destroy']
        )->name('roles.permissions.destroy');

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Admin Notifications
        |--------------------------------------------------------------------------
        */

        Route::put(
            'customizations/{customization}/status',
            [AdminCustomization::class, 'updateStatus']
        );


        /*
        |--------------------------------------------------------------------------
        | Customers Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'customers',
            CustomerController::class
        )->except([
            'create',
            'edit',
        ]);

        Route::patch(
            'customers/{customer}/restore',
            [CustomerController::class, 'restore']
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

        Route::apiResource(
            'product-attribute-values',
            ProductAttributeValueController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Product Customizations
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Raw Materials
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'raw-materials',
            RawMaterialController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Colors
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'colors',
            ColorController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Design Patterns
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'design-patterns',
            DesignPatternController::class
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


        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'orders',
            AdminOrder::class
        );

        Route::put(
            'orders/{order}/status',
            [OrderStatusController::class, 'update']
        );

        Route::post(
            'admin-users/{adminUser}/password-reset',
            [AdminPasswordResetController::class, 'store']
        );

        Route::delete(
            'password-resets/{reset}',
            [AdminPasswordResetController::class, 'destroy']
        );

        Route::post(
            'orders/{order}/next-stage',
            [OrderProductionController::class, 'changeStage']
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
        | Payments
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


        /*
        |--------------------------------------------------------------------------
        | Reviews
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Activity Logs
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'activity-logs',
            ActivityLogController::class
        )->only([
            'index',
            'show',
        ]);
    });


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
| Customer API
|--------------------------------------------------------------------------
|
| Every protected customer endpoint requires an authenticated Customer.
|
*/

Route::prefix('customer')
    ->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Authentication
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


        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'orders',
            CustomerOrder::class
        )
            ->except([
                'update',
                'destroy'
            ]);


        /*
        |--------------------------------------------------------------------------
        | Cart
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Favorites
        |--------------------------------------------------------------------------
        */

        Route::get(
            'favorites',
            [FavoriteController::class, 'index']
        );

        Route::post(
            'favorites/{product}',
            [FavoriteController::class, 'toggle']
        );


        /*
        |--------------------------------------------------------------------------
        | Addresses
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'addresses',
            AddressController::class
        );

        Route::put(
            'addresses/{address}/default',
            [AddressController::class, 'setDefault']
        );


        /*
        |--------------------------------------------------------------------------
        | Verification
        |--------------------------------------------------------------------------
        */

        Route::post(
            'verifications/generate',
            [VerificationController::class, 'generate']
        );

        Route::post(
            'verifications/verify',
            [VerificationController::class, 'verify']
        );


        /*
        |--------------------------------------------------------------------------
        | Reviews
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Customer Notifications
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */

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
