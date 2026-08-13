<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Address;
use App\Models\AdminNotification;
use App\Models\AdminPasswordReset;
use App\Models\AdminUser;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Color;
use App\Models\CustomDesignRequest;
use App\Models\Customer;
use App\Models\CustomerNotification;
use App\Models\DesignPattern;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderProductionStage;
use App\Models\OrderProductionStageHistory;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductCustomizationRequest;
use App\Models\ProductMedia;
use App\Models\RawMaterial;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    private const ROLES_COUNT = 5;
    private const PERMISSIONS_COUNT = 20;
    private const ADMIN_USERS_COUNT = 6;
    private const ADMIN_PASSWORD_RESETS_COUNT = 4;
    private const ADMIN_NOTIFICATIONS_COUNT = 15;
    private const ACTIVITY_LOGS_COUNT = 25;
    private const FRAMEWORK_USERS_COUNT = 5;
    private const CUSTOMERS_COUNT = 30;
    private const ADDRESSES_PER_CUSTOMER = 2;
    private const VERIFICATION_CODES_COUNT = 20;
    private const CUSTOMER_NOTIFICATIONS_COUNT = 60;
    private const ROOT_CATEGORIES_COUNT = 8;
    private const CHILD_CATEGORIES_COUNT = 12;
    private const COLORS_COUNT = 12;
    private const DESIGN_PATTERNS_COUNT = 8;
    private const PRODUCT_ATTRIBUTES_COUNT = 6;
    private const PRODUCTS_COUNT = 50;
    private const MEDIA_PER_PRODUCT = 3;
    private const CUSTOM_DESIGN_REQUESTS_COUNT = 15;
    private const PRODUCT_CUSTOMIZATION_REQUESTS_COUNT = 20;
    private const CARTS_COUNT = 20;
    private const ORDERS_COUNT = 60;
    private const ORDER_ITEMS_COUNT = 150;
    private const CUSTOMIZED_ORDER_ITEMS_COUNT = 15;
    private const ORDER_STATUS_HISTORY_COUNT = 120;
    private const PAID_ORDERS_COUNT = 50;
    private const PRODUCTION_STAGE_HISTORY_COUNT = 90;
    private const REVIEWED_ITEMS_COUNT = 60;
    private const REVIEW_IMAGES_COUNT = 80;
    private const RAW_MATERIALS_COUNT = 15;

    private Collection $roles;
    private Collection $permissions;
    private Collection $adminUsers;
    private Collection $customers;
    private Collection $categories;
    private Collection $colors;
    private Collection $designPatterns;
    private Collection $attributes;
    private Collection $products;
    private Collection $carts;
    private Collection $productionStages;
    private Collection $orders;
    private Collection $orderItems;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedAccessControl();
            $this->seedAdminStaff();
            $this->seedFrameworkUsers();
            $this->seedCustomers();
            $this->seedCatalogLookups();
            $this->seedCategoriesAndProducts();
            $this->seedFavoritesAndCarts();
            $this->seedCustomizationSystem();
            $this->seedProductionStages();
            $this->seedOrders();
            $this->seedReviews();
            $this->seedRawMaterials();
        });
    }

    /**
     * Module 1a: Roles, permissions, and the role<->permission pivot.
     */
    private function seedAccessControl(): void
    {
        /**
         * Create roles
         */
        $this->roles = collect();


        $this->roles->push(
            Role::factory()->create([
                'name' => 'Super Admin',
                'description' => 'Full system access'
            ])
        );


        Role::factory()
            ->count(self::ROLES_COUNT - 1)
            ->create()
            ->each(function ($role) {
                $this->roles->push($role);
            });


        /**
         * Fixed permissions with modules
         */
        $permissions = [

            // Admin Users
            [
                'name' => 'admin_users.view',
                'module' => 'admin_users',
            ],
            [
                'name' => 'admin_users.create',
                'module' => 'admin_users',
            ],
            [
                'name' => 'admin_users.update',
                'module' => 'admin_users',
            ],
            [
                'name' => 'admin_users.delete',
                'module' => 'admin_users',
            ],


            // Notifications
            [
                'name' => 'notifications.view',
                'module' => 'notifications',
            ],
            [
                'name' => 'notifications.read',
                'module' => 'notifications',
            ],


            // Orders
            [
                'name' => 'orders.view',
                'module' => 'orders',
            ],
            [
                'name' => 'orders.create',
                'module' => 'orders',
            ],
            [
                'name' => 'orders.update',
                'module' => 'orders',
            ],
            [
                'name' => 'orders.delete',
                'module' => 'orders',
            ],


            // Products
            [
                'name' => 'products.view',
                'module' => 'products',
            ],
            [
                'name' => 'products.create',
                'module' => 'products',
            ],
            [
                'name' => 'products.update',
                'module' => 'products',
            ],
            [
                'name' => 'products.delete',
                'module' => 'products',
            ],


            // Customers
            [
                'name' => 'customers.view',
                'module' => 'customers',
            ],
            [
                'name' => 'customers.update',
                'module' => 'customers',
            ],


            // Reports
            [
                'name' => 'reports.view',
                'module' => 'reports',
            ],
        ];


        $this->permissions = collect($permissions)
            ->map(function ($permission) {

                return Permission::firstOrCreate(
                    [
                        'name' => $permission['name'],
                    ],
                    [
                        'module' => $permission['module'],
                    ]
                );
            });


        /**
         * Give all permissions to first role
         * (Super Admin)
         */
        $adminRole = $this->roles->where('name', 'Super Admin')->first();

        if (!$adminRole) {
            $adminRole = $this->roles->first();
        }


        foreach ($this->permissions as $permission) {

            RolePermission::updateOrCreate(
                [
                    'role_id' => $adminRole->id,
                    'permission_id' => $permission->id,
                ]
            );
        }
    }
    /**
     * Module 1b: Admin/back-office accounts and their activity trail.
     */
    private function seedAdminStaff(): void
    {
        
          $this->adminUsers = AdminUser::factory()
           ->count(self::ADMIN_USERS_COUNT)
          ->recycle($this->roles)
          ->create();
         



        AdminPasswordReset::factory()
            ->count(self::ADMIN_PASSWORD_RESETS_COUNT)
            ->recycle($this->adminUsers)
            ->create();

        AdminNotification::factory()
            ->count(self::ADMIN_NOTIFICATIONS_COUNT)
            ->recycle($this->adminUsers)
            ->create();

        ActivityLog::factory()
            ->count(self::ACTIVITY_LOGS_COUNT)
            ->recycle($this->adminUsers)
            ->create();
    }

    /**
     * Framework default `users` table - not referenced by any domain
     * table, included only because a Factory exists for it.
     */
    private function seedFrameworkUsers(): void
    {
        User::factory()->count(self::FRAMEWORK_USERS_COUNT)->create();
    }

    /**
     * Module 2: Storefront customer accounts and related records.
     */
    private function seedCustomers(): void
    {
        $this->customers = Customer::factory()
            ->count(self::CUSTOMERS_COUNT)
            ->has(Address::factory()->count(self::ADDRESSES_PER_CUSTOMER), 'addresses')
            ->create();

        VerificationCode::factory()
            ->count(self::VERIFICATION_CODES_COUNT)
            ->recycle($this->customers)
            ->create();

        CustomerNotification::factory()
            ->count(self::CUSTOMER_NOTIFICATIONS_COUNT)
            ->recycle($this->customers)
            ->create();
    }

    /**
     * Module 3a: Independent catalog lookup tables.
     */
    private function seedCatalogLookups(): void
    {
        $this->colors = Color::factory()->count(self::COLORS_COUNT)->create();
        $this->designPatterns = DesignPattern::factory()->count(self::DESIGN_PATTERNS_COUNT)->create();
        $this->attributes = ProductAttribute::factory()->count(self::PRODUCT_ATTRIBUTES_COUNT)->create();
    }

    /**
     * Module 3b: Category tree, products, and their media/colors/attributes.
     */
    private function seedCategoriesAndProducts(): void
    {
        $rootCategories = ProductCategory::factory()->count(self::ROOT_CATEGORIES_COUNT)->create();

        $childCategories = ProductCategory::factory()
            ->count(self::CHILD_CATEGORIES_COUNT)
            ->state(fn() => ['parent_id' => $rootCategories->random()->id])
            ->create();

        $this->categories = $rootCategories->merge($childCategories);

        $this->products = Product::factory()
            ->count(self::PRODUCTS_COUNT)
            ->recycle($this->categories)
            ->has(ProductMedia::factory()->count(self::MEDIA_PER_PRODUCT), 'media')
            ->create();

        $this->products->each(function (Product $product) {
            $assignedColors = $this->colors->random(min(random_int(1, 4), $this->colors->count()));

            foreach ($assignedColors as $color) {
                ProductColor::factory()->create([
                    'product_id' => $product->id,
                    'color_id' => $color->id,
                ]);
            }

            $assignedAttributes = $this->attributes->random(min(random_int(2, 4), $this->attributes->count()));

            foreach ($assignedAttributes as $attribute) {
                ProductAttributeValue::factory()->create([
                    'product_id' => $product->id,
                    'attribute_id' => $attribute->id,
                ]);
            }
        });
    }

    /**
     * Module 4: Wishlists, one cart per selected customer, and cart lines.
     */
    private function seedFavoritesAndCarts(): void
    {
        $this->customers->each(function (Customer $customer) {
            $favoriteCount = random_int(0, 5);

            if ($favoriteCount === 0) {
                return;
            }

            $favorited = $this->products->random(min($favoriteCount, $this->products->count()));

            foreach ($favorited as $product) {
                Favorite::factory()->create([
                    'customer_id' => $customer->id,
                    'product_id' => $product->id,
                ]);
            }
        });

        $cartOwners = $this->customers->random(min(self::CARTS_COUNT, $this->customers->count()));

        $this->carts = $cartOwners->map(
            fn(Customer $customer) => Cart::factory()->create(['customer_id' => $customer->id])
        );

        $this->carts->each(function (Cart $cart) {
            $items = $this->products->random(min(random_int(1, 4), $this->products->count()));

            foreach ($items as $product) {
                CartItem::factory()->create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                ]);
            }
        });
    }

    /**
     * Module 5: Free-form and product-based customization requests.
     */
    private function seedCustomizationSystem(): void
    {
        CustomDesignRequest::factory()
            ->count(self::CUSTOM_DESIGN_REQUESTS_COUNT)
            ->recycle($this->customers)
            ->create();

        ProductCustomizationRequest::factory()
            ->count(self::PRODUCT_CUSTOMIZATION_REQUESTS_COUNT)
            ->recycle([$this->customers, $this->products, $this->colors, $this->designPatterns])
            ->create();
    }

    /**
     * Module 8a: Fixed production-stage lookup (must exist before orders).
     */
    private function seedProductionStages(): void
    {
        $this->productionStages = OrderProductionStage::factory()->count(5)->create();
    }

    /**
     * Module 6: Orders, line items, status trail, payments, and the
     * production-stage transition history.
     */
    private function seedOrders(): void
    {
        $addresses = $this->customers->flatMap(fn(Customer $customer) => $customer->addresses);

        $this->orders = Order::factory()
            ->count(self::ORDERS_COUNT)
            ->recycle([$this->customers, $addresses, $this->productionStages])
            ->create();

        $standardItems = OrderItem::factory()
            ->count(self::ORDER_ITEMS_COUNT)
            ->recycle([$this->orders, $this->products])
            ->create();

        $customers = Customer::all();
        $products = Product::all();
        $colors = Color::all();
        $designPatterns = DesignPattern::all();

        $customizedItems = OrderItem::factory()
            ->count(self::CUSTOMIZED_ORDER_ITEMS_COUNT)
            ->recycle([$this->orders, $this->products])
            ->state(function () use ($customers, $products, $colors, $designPatterns) {
                return [
                    'is_customized' => true,
                    'product_customization_request_id' => ProductCustomizationRequest::factory()
                        ->recycle([
                            $customers,
                            $products,
                            $colors,
                            $designPatterns,
                        ])
                        ->create()
                        ->id,
                ];
            })
            ->create();

        $this->orderItems = $standardItems->merge($customizedItems);

        OrderStatusHistory::factory()
            ->count(self::ORDER_STATUS_HISTORY_COUNT)
            ->recycle([$this->orders, $this->adminUsers])
            ->create();

        $payableOrders = $this->orders->random(min(self::PAID_ORDERS_COUNT, $this->orders->count()));

        foreach ($payableOrders as $order) {
            Payment::factory()->create(['order_id' => $order->id]);
        }

        OrderProductionStageHistory::factory()
            ->count(self::PRODUCTION_STAGE_HISTORY_COUNT)
            ->recycle([$this->orders, $this->productionStages, $this->adminUsers])
            ->create();
    }

    /**
     * Module 7: Reviews (one per selected order item) and their images.
     */
    private function seedReviews(): void
    {
        $orderCustomerMap = $this->orders->pluck('customer_id', 'id');

        $reviewedItems = $this->orderItems->random(min(self::REVIEWED_ITEMS_COUNT, $this->orderItems->count()));

        $reviews = $reviewedItems->map(fn(OrderItem $item) => Review::factory()->create([
            'customer_id' => $orderCustomerMap[$item->order_id],
            'product_id' => $item->product_id,
            'order_item_id' => $item->id,
        ]));

        ReviewImage::factory()
            ->count(self::REVIEW_IMAGES_COUNT)
            ->recycle($reviews)
            ->create();
    }

    /**
     * Module 8b: Raw-material procurement/inventory stock.
     */
    private function seedRawMaterials(): void
    {
        RawMaterial::factory()->count(self::RAW_MATERIALS_COUNT)->create();
    }
}
