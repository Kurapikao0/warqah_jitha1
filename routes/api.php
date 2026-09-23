<?php

use App\Http\Controllers\API\Admin\ActivityLogController;
use App\Http\Controllers\API\Admin\AdminNotificationController;
use App\Http\Controllers\API\Admin\AdminPasswordResetController;
use App\Http\Controllers\API\Admin\AdminProfileController;
use App\Http\Controllers\API\Admin\AdminUserController;
use App\Http\Controllers\API\Admin\ColorController;
use App\Http\Controllers\API\Admin\CustomerController;
use App\Http\Controllers\API\Admin\DesignPatternController;
use App\Http\Controllers\API\Admin\ExchangeRateController;
use App\Http\Controllers\API\Admin\CustomDesignRequestController;
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
use App\Http\Controllers\API\Admin\SystemSettingController;

use App\Http\Controllers\API\Auth\AdminAuthController;
use App\Http\Controllers\API\Auth\CustomerAuthController;
use App\Http\Controllers\API\Auth\GoogleAuthController;
use App\Http\Controllers\API\Customer\AddressController;
use App\Http\Controllers\API\Customer\CartController;
use App\Http\Controllers\API\Customer\CartItemController;
use App\Http\Controllers\API\Customer\CustomerNotificationController;
use App\Http\Controllers\API\Customer\FavoriteController;
use App\Http\Controllers\API\Customer\OrderController as CustomerOrder;
use App\Http\Controllers\API\Customer\PasswordResetController;
use App\Http\Controllers\API\Customer\PaymentController as CustomerPayment;
use App\Http\Controllers\API\Customer\ProductCustomizationController as CustomerCustomization;
use App\Http\Controllers\API\Customer\ProfileController;
use App\Http\Controllers\API\Customer\ReviewController;
use App\Http\Controllers\API\Customer\ReviewImageController;
use App\Http\Controllers\API\Customer\VerificationController;
use App\Http\Controllers\API\Customer\ProductCatalogController;
use App\Http\Controllers\API\Customer\CategoryCatalogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Health Check & Tests
|--------------------------------------------------------------------------
*/
Route::get('/test', function () {
    return response()->json([
        'status' => true,
        'message' => 'API is working'
    ], 200);
});

Route::get('/test-admin-auth', function () {
    return response()->json([
        'admin' => auth('admin')->user(),
        'sanctum' => auth()->user(),
    ]);
})->middleware('auth:admin');

Route::get('products', [\App\Http\Controllers\API\Public\ProductController::class, 'index']);
Route::get('products/price-range', [\App\Http\Controllers\API\Public\ProductController::class, 'priceRange']);
Route::get('products/{id}/reviews', [\App\Http\Controllers\API\Public\ProductController::class, 'reviews'])->whereNumber('id');
Route::get('products/{id}', [\App\Http\Controllers\API\Public\ProductController::class, 'show'])->whereNumber('id');
Route::get('categories', [\App\Http\Controllers\API\Public\CategoryController::class, 'index']);
Route::get('settings', [\App\Http\Controllers\API\Public\SettingController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Public Product Catalog
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductCatalogController::class, 'index']);
Route::get('/products/{id}', [ProductCatalogController::class, 'show']);
Route::get('/categories', [CategoryCatalogController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Customer Authentication - Canonical Routes
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->group(function (): void {
    Route::post('register', [CustomerAuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('login', [CustomerAuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('password/forgot', [PasswordResetController::class, 'forgotPassword'])->middleware('throttle:5,1');
    Route::post('password/reset', [PasswordResetController::class, 'resetPassword'])->middleware('throttle:5,1');
});

Route::middleware('web')->prefix('auth')->group(function (): void {
    Route::get('google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    Route::post(
        'register',
        [CustomerAuthController::class, 'register']
    )->middleware('throttle:auth');

    Route::post(
        'login',
        [CustomerAuthController::class, 'login']
    )->middleware('throttle:auth');

    Route::post(
        'password/forgot',
        [PasswordResetController::class, 'forgotPassword']
    )->middleware('throttle:auth');

    Route::post(
        'password/reset',
        [PasswordResetController::class, 'resetPassword']
    )->middleware('throttle:auth');
});

Route::post('auth/google/exchange', [GoogleAuthController::class, 'exchangeCode'])
    ->middleware('throttle:10,1');

/*
|--------------------------------------------------------------------------
| Customer Authentication - Legacy Aliases
|--------------------------------------------------------------------------
*/
Route::post('register', [CustomerAuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('login', [CustomerAuthController::class, 'login'])->middleware('throttle:5,1');

Route::post(
    'register',
    [CustomerAuthController::class, 'register']
)->middleware('throttle:auth');

Route::post(
    'login',
    [CustomerAuthController::class, 'login']
)->middleware('throttle:auth');

/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/
Route::prefix('admin/auth')->group(function (): void {
    Route::post('login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('logout', [AdminAuthController::class, 'logout'])->middleware('auth:admin');

    Route::post(
        'login',
        [AdminAuthController::class, 'login']
    )->middleware('throttle:auth');

    Route::post(
        'logout',
        [AdminAuthController::class, 'logout']
    )->middleware('auth:admin');
});

/*
|--------------------------------------------------------------------------
| Customer Verification - Public
|--------------------------------------------------------------------------
*/
Route::post('customer/verifications/generate', [VerificationController::class, 'generate']);
Route::post('customer/verifications/verify', [VerificationController::class, 'verify']);

/*
|--------------------------------------------------------------------------
| Admin Protected API
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware('auth:admin')
    ->group(function (): void {

        // Profile & Settings
        Route::get('profile', [AdminProfileController::class, 'show']);
        Route::put('profile', [AdminProfileController::class, 'update']);

        Route::get('notifications', function (\Illuminate\Http\Request $request) {
            return response()->json([
                'data' => $request->user('admin')->unreadNotifications
            ]);
        });

        Route::post('/notifications/mark-as-read', function (\Illuminate\Http\Request $request) {
            $request->user('admin')->unreadNotifications()->update(['read_at' => now()]);
            return response()->json(['success' => true]);
        });

        Route::get('/test-notification', function () {
            $admins = \App\Models\Admin::all();
            $product = \App\Models\Product::first();
            
            if ($admins->isEmpty() || !$product) {
                return response()->json(['error' => 'Ensure you have at least one Admin and one Product in the database.'], 400);
            }

            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\LowStockNotification($product));
            
            return response()->json([
                'message' => 'Test notification dispatched successfully!',
                'product' => $product->name
            ]);
        });

        Route::get('settings', [SystemSettingController::class, 'show']);
        Route::put('settings', [SystemSettingController::class, 'update']);
        Route::get('exchange-rates', [ExchangeRateController::class, 'index']);

        // Roles & Permissions
        Route::apiResource('roles', RoleController::class);
        Route::get('roles/{role}/permissions', [RolePermissionController::class, 'index'])->name('roles.permissions.index');
        Route::match(['post', 'put'], 'roles/{role}/permissions', [RolePermissionController::class, 'store'])->name('roles.permissions.store');
        Route::delete('roles/{role}/permissions/{permission}', [RolePermissionController::class, 'destroy'])->name('roles.permissions.destroy');
        Route::apiResource('permissions', PermissionController::class);

        // Admin Users
        Route::apiResource('admin-users', AdminUserController::class);
        Route::post('admin-users/{adminUser}/password-reset', [AdminPasswordResetController::class, 'store']);
        Route::delete('password-resets/{reset}', [AdminPasswordResetController::class, 'destroy']);
        Route::get('admin-users/{adminUser}/notifications', [AdminNotificationController::class, 'index']);
        Route::get('notifications', [AdminNotificationController::class, 'mine']);
        Route::put('notifications/{notification}/read', [AdminNotificationController::class, 'read']);

        // Customers Management
        Route::apiResource('customers', CustomerController::class)->except(['create', 'edit']);
        Route::patch('customers/{customer}/restore', [CustomerController::class, 'restore']);
        Route::patch('customers/{customer}/status', [CustomerController::class, 'changeStatus']);
        Route::patch('customers/{customer}/verify', [CustomerController::class, 'verify']);

        // Products & Media
        Route::apiResource('products', ProductController::class);
        Route::apiResource('product-categories', ProductCategoryController::class)->parameters(['product-categories' => 'category']);
        Route::post('product-media/upload', [ProductMediaController::class, 'upload']);
        Route::put('product-media/reorder', [ProductMediaController::class, 'reorder']);
        Route::put('product-media/{productMedia}/primary', [ProductMediaController::class, 'setPrimary']);
        Route::apiResource('product-media', ProductMediaController::class);
        Route::apiResource('product-attributes', ProductAttributeController::class);
        Route::apiResource('product-attribute-values', ProductAttributeValueController::class);

        // Product Customizations
        Route::get('customizations', [AdminCustomization::class, 'index']);
        Route::post('customizations', [AdminCustomization::class, 'store']);
        Route::get('customizations/{id}', [AdminCustomization::class, 'show']);
        Route::delete('customizations/{customization}', [AdminCustomization::class, 'destroy']);
        Route::put('customizations/{customization}/status', [AdminCustomization::class, 'updateStatus']);

        // Materials, Colors, Patterns & Stages
        Route::apiResource('raw-materials', RawMaterialController::class);
        Route::apiResource('colors', ColorController::class);
        Route::apiResource('patterns', DesignPatternController::class);
        Route::apiResource('design-patterns', DesignPatternController::class);
        Route::apiResource('production-stages', OrderProductionStageController::class);

        // Orders & Production
        Route::apiResource('custom-design-requests', CustomDesignRequestController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('orders', AdminOrder::class)->names('admin.orders');
        Route::put('orders/{order}/status', [OrderStatusController::class, 'update']);
        Route::get('orders-statistics', [AdminOrder::class, 'statistics']);
        Route::get('orders/{order}/production-history', [OrderProductionController::class, 'history']);
        Route::post('orders/{order}/next-stage', [OrderProductionController::class, 'changeStage']);
        Route::post('orders/{order}/stage/{stageId}', [OrderProductionController::class, 'updateStage']);
        Route::get('orders/{order}/status-history', [OrderStatusHistoryController::class, 'index']);

        // Payments
        Route::get('payments', [AdminPayment::class, 'index']);
        Route::get('payments/{id}', [AdminPayment::class, 'show']);
        Route::put('payments/{payment}/status', [AdminPayment::class, 'updateStatus']);
        Route::delete('payments/{payment}', [AdminPayment::class, 'destroy']);

        // Reviews
        Route::get('reviews', [AdminReviewController::class, 'index']);
        Route::match(['put', 'patch'], 'reviews/{review}/status', [AdminReviewController::class, 'updateStatus']);
        Route::match(['post', 'patch'], 'reviews/{review}/reply', [AdminReviewController::class, 'reply']);
        Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy']);

        // Activity Logs
        Route::apiResource('activity-logs', ActivityLogController::class)->only(['index', 'show']);
        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'roles',
            RoleController::class
        );

        // Role Permissions
        Route::get(
            'roles/{role}/permissions',
            [RolePermissionController::class, 'index']
        )->name('roles.permissions.index');

        Route::match(
            ['post', 'put'],
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

        /*
        |--------------------------------------------------------------------------
        | Admin Users
        |--------------------------------------------------------------------------
        */

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

        Route::get(
            'notifications',
            [AdminNotificationController::class, 'mine']
        );

        /*
        |--------------------------------------------------------------------------
        | Admin Notifications
        |--------------------------------------------------------------------------
        */

        Route::put(
            'notifications/{notification}/read',
            [AdminNotificationController::class, 'read']
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
        | Products
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'products',
            ProductController::class
        );

        Route::apiResource(
            'product-categories',
            ProductCategoryController::class
        )->parameters(['product-categories' => 'category']);

        // Specialized Product Media routes — must be declared BEFORE apiResource
        // so 'upload' and 'reorder' static segments take priority over {productMedia}.
        Route::post(
            'product-media/upload',
            [ProductMediaController::class, 'upload']
        );

        Route::put(
            'product-media/reorder',
            [ProductMediaController::class, 'reorder']
        );

        Route::put(
            'product-media/{productMedia}/primary',
            [ProductMediaController::class, 'setPrimary']
        );

        Route::apiResource(
            'product-media',
            ProductMediaController::class
        );

        Route::apiResource(
            'product-attributes',
            ProductAttributeController::class
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
            'customizations',
            [AdminCustomization::class, 'index']
        );

        Route::post(
            'customizations',
            [AdminCustomization::class, 'store']
        );

        Route::get(
            'customizations/{id}',
            [AdminCustomization::class, 'show']
        );

        Route::delete(
            'customizations/{customization}',
            [AdminCustomization::class, 'destroy']
        );

        Route::put(
            'customizations/{customization}/status',
            [AdminCustomization::class, 'updateStatus']
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
            'patterns',
            DesignPatternController::class
        );

        Route::apiResource(
            'design-patterns',
            DesignPatternController::class
        );

        /*
        |--------------------------------------------------------------------------
        | Production Stages
        |--------------------------------------------------------------------------
        */
        Route::post(
            'production-stages/reorder',
            [OrderProductionStageController::class, 'reorder']
        );

        Route::apiResource(
            'production-stages',
            OrderProductionStageController::class
        );

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */
        Route::delete(
            'custom-design-requests/{customDesignRequest}/images/{image}',
            [CustomDesignRequestController::class, 'destroyImage']
        );
        Route::apiResource(
            'custom-design-requests',
            CustomDesignRequestController::class
        )->only(['index', 'store', 'show', 'update', 'destroy']);



        Route::apiResource(
            'orders',
            AdminOrder::class
        )->names('admin.orders');

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

        /*
        |--------------------------------------------------------------------------
        | Payments
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

        Route::delete(
            'payments/{payment}',
            [AdminPayment::class, 'destroy']
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

        Route::match(
            ['put', 'patch'],
            'reviews/{review}/status',
            [AdminReviewController::class, 'updateStatus']
        );

        Route::match(
            ['post', 'patch'],
            'reviews/{review}/reply',
            [AdminReviewController::class, 'reply']
        );

        Route::delete(
            'reviews/{review}',
            [AdminReviewController::class, 'destroy']
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

/*
|--------------------------------------------------------------------------
| Customer Protected API
|--------------------------------------------------------------------------
*/
Route::prefix('customer')
    ->middleware('auth:customer')
    ->group(function (): void {

        // Authentication
        Route::post('logout', [CustomerAuthController::class, 'logout']);

        // Customizations & Orders
        Route::apiResource('customizations', CustomerCustomization::class);
        Route::get('orders/{order}/tracking', [CustomerOrder::class, 'tracking'])->name('customer.orders.tracking');
        Route::apiResource('orders', CustomerOrder::class)->except(['update', 'destroy'])->names('customer.orders');

        // Cart
        Route::get('cart', [CartController::class, 'index']);
        Route::delete('cart', [CartController::class, 'clear']);
        Route::post('cart/sync', [CartController::class, 'sync']);
        Route::post('cart/renew', [CartController::class, 'renew']);
        Route::post('cart/reserve', [CartController::class, 'renew']);
        Route::post('cart/release', [CartController::class, 'release']);
        Route::post('cart/items', [CartItemController::class, 'store']);
        Route::put('cart/items/{cartItem}', [CartItemController::class, 'update']);
        Route::post('cart/items/{cartItem}/rereserve', [CartItemController::class, 'rereserve']);
        Route::delete('cart/items/{cartItem}', [CartItemController::class, 'destroy']);

        // Favorites
        Route::get('favorites', [FavoriteController::class, 'index']);
        Route::post('favorites/{product}', [FavoriteController::class, 'toggle']);

        // Addresses
        Route::match(['put', 'patch'], 'addresses/{address}/default', [AddressController::class, 'setDefault']);
        Route::apiResource('addresses', AddressController::class);

        // Reviews
        Route::post('products/{product}/reviews', [ReviewController::class, 'storeForProduct']);
        Route::apiResource('reviews', ReviewController::class)->withTrashed(['destroy']);
        Route::post('reviews/{review}/images', [ReviewImageController::class, 'store']);
        Route::delete('review-images/{reviewImage}', [ReviewImageController::class, 'destroy']);

        // Customer Notifications
        Route::get('notifications', [CustomerNotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/unread-count', [CustomerNotificationController::class, 'unreadCount'])->name('notifications.unread-count');
        Route::patch('notifications/read-all', [CustomerNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::get('notifications/{id}', [CustomerNotificationController::class, 'show'])->name('notifications.show');
        Route::patch('notifications/{notification}/read', [CustomerNotificationController::class, 'read'])->name('notifications.read');
        Route::delete('notifications/{notification}', [CustomerNotificationController::class, 'destroy'])->name('notifications.destroy');

        // Payments & Profile
        Route::apiResource('payments', CustomerPayment::class)->only(['index', 'store', 'show']);
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);
        Route::delete('profile', [ProfileController::class, 'destroy']);
        Route::put('profile/password', [ProfileController::class, 'updatePassword']);
        Route::post('profile/avatar', [ProfileController::class, 'updateAvatar']);
    });
    
