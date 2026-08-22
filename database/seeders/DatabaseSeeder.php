<?php

namespace Database\Seeders;

use App\Enums\VerificationPurpose;
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
use Illuminate\Support\Facades\Hash;
use LogicException;

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

    private function seedAccessControl(): void
    {
        $superAdminRole = Role::query()->updateOrCreate(
            ['name' => 'super-admin'],
            [
                'description' => 'مدير النظام',
            ]
        );

        $roleNames = [
            'مدير المتجر',
            'موظف الطلبات',
            'موظف الإنتاج',
            'موظف خدمة العملاء',
        ];

        $otherRoles = collect();

        foreach ($roleNames as $name) {
            $otherRoles->push(
                Role::query()->create([
                    'name' => $name,
                    'description' => 'صلاحيات خاصة بإدارة النظام',
                ])
            );
        }

        $this->roles = collect([$superAdminRole])->merge($otherRoles);

        $permissionNames = [
            'إدارة المستخدمين',
            'إدارة العملاء',
            'إدارة المنتجات',
            'إدارة التصنيفات',
            'إدارة الطلبات',
            'إدارة المدفوعات',
            'إدارة المخزون',
            'إدارة المواد الخام',
            'إدارة الألوان',
            'إدارة التصاميم',
            'إدارة التخصيص',
            'إدارة المراجعات',
            'عرض التقارير',
            'إدارة الإشعارات',
            'إدارة الموظفين',
            'إدارة الصلاحيات',
            'إدارة العربات',
            'إدارة المفضلة',
            'إدارة الإنتاج',
            'إعدادات النظام',
        ];

        $this->permissions = collect();

        foreach ($permissionNames as $index => $displayName) {
            $permission = Permission::factory()->create();

            /*
             * نحاول جعل الاسم عربيًا إذا كان الحقل موجودًا في الموديل.
             * لا نعتمد عليه في الإنشاء حتى لا نكسر الـ migration.
             */
            if (isset($permission->name)) {
                $permission->update([
                    'name' => 'permission_' . ($index + 1),
                ]);
            }

            if ($permission->getAttribute('display_name') !== null) {
                $permission->update([
                    'display_name' => $displayName,
                ]);
            }

            $this->permissions->push($permission);
        }

        $this->roles->each(function (Role $role) {
            $assigned = $this->permissions->random(
                min(random_int(4, 8), $this->permissions->count())
            );

            foreach ($assigned as $permission) {
                RolePermission::firstOrCreate([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                ]);
            }
        });
    }

    private function seedAdminStaff(): void
    {
        $superAdminRole = $this->roles
            ->where('name', 'super-admin')
            ->first();

        $superAdmin = AdminUser::query()->updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'full_name' => 'مدير النظام',
                'password_hash' => Hash::make('p@ssword123!'),
                'role_id' => $superAdminRole->id,
            ]
        );

        $adminNames = [
            'مدير المتجر',
            'مسؤول الطلبات',
            'مسؤول الإنتاج',
            'مسؤول خدمة العملاء',
            'مسؤول المخزون',
        ];

        $otherAdmins = collect();

        foreach ($adminNames as $name) {
            $otherAdmins->push(
                AdminUser::factory()
                    ->recycle($this->roles)
                    ->create([
                        'full_name' => $name,
                    ])
            );
        }

        $this->adminUsers = collect([$superAdmin])->merge($otherAdmins);

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

    private function seedFrameworkUsers(): void
    {
        User::factory()
            ->count(self::FRAMEWORK_USERS_COUNT)
            ->create();
    }

    private function seedCustomers(): void
    {
        $customerNames = [
            'محمد أحمد',
            'علي محمد',
            'سارة علي',
            'خالد حسن',
            'فاطمة عبدالله',
            'أحمد صالح',
            'ريم محمد',
            'عبدالله حسين',
            'نور أحمد',
            'مريم علي',
            'ياسر خالد',
            'سمية حسن',
            'إبراهيم محمد',
            'هدى صالح',
            'مازن عبدالله',
            'ليان أحمد',
            'عمر حسين',
            'نجلاء محمد',
            'حسن علي',
            'أسماء خالد',
            'طارق صالح',
            'إيمان عبدالله',
            'سليم أحمد',
            'روان حسن',
            'بدر محمد',
            'شيماء علي',
            'أنس خالد',
            'خلود صالح',
            'وليد عبدالله',
            'جنى محمد',
        ];

        $this->customers = Customer::factory()
            ->count(self::CUSTOMERS_COUNT)
            ->has(
                Address::factory()->count(self::ADDRESSES_PER_CUSTOMER),
                'addresses'
            )
            ->create();

        $this->customers->each(function (Customer $customer, $index) use ($customerNames) {
            $customer->update([
                'full_name' => $customerNames[$index],
            ]);
        });

        $verificationPurposes = [
            VerificationPurpose::SignupEmailVerification,
            VerificationPurpose::PasswordResetEmailLink,
            VerificationPurpose::ChangeEmailVerification,
        ];

        $verificationCodes = $this->customers
            ->take(self::VERIFICATION_CODES_COUNT)
            ->map(function (Customer $customer, int $index) use ($verificationPurposes) {
                $purpose = $verificationPurposes[$index % count($verificationPurposes)];

                return [
                    'customer_id' => $customer->id,
                    'purpose' => $purpose->value,

                    'code_or_token' => match ($purpose) {
                        VerificationPurpose::SignupEmailVerification,
                        VerificationPurpose::ChangeEmailVerification =>
                        (string) random_int(100000, 999999),

                        VerificationPurpose::PasswordResetEmailLink =>
                        bin2hex(random_bytes(32)),

                        default =>
                        throw new LogicException(
                            "Unsupported verification purpose: {$purpose->value}"
                        ),
                    },

                    'contact_value' => $customer->email,
                    'expires_at' => now()->addMinutes(10),
                    'consumed_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->all();

        VerificationCode::insert($verificationCodes);

        CustomerNotification::factory()
            ->count(self::CUSTOMER_NOTIFICATIONS_COUNT)
            ->recycle($this->customers)
            ->create();
    }

    private function seedCatalogLookups(): void
    {
        $colorNames = [
            'أبيض',
            'أسود',
            'بني',
            'بيج',
            'رمادي',
            'ذهبي',
            'فضي',
            'أحمر',
            'أزرق',
            'أخضر',
            'عاجي',
            'خشبي',
        ];

        $this->colors = collect();

        foreach ($colorNames as $name) {
            $this->colors->push(
                Color::factory()->create([
                    'name' => $name,
                ])
            );
        }

        $patternNames = [
            'نقشة هندسية',
            'نقشة خشبية',
            'نقشة عربية',
            'نقشة تراثية',
            'نقشة عصرية',
            'نقشة بسيطة',
            'نقشة زخرفية',
            'نقشة طبيعية',
        ];

        $this->designPatterns = collect();

        foreach ($patternNames as $name) {
            $this->designPatterns->push(
                DesignPattern::factory()->create([
                    'name' => $name,
                ])
            );
        }

        /*
         * display_name مطلوب في قاعدة البيانات.
         * لذلك لا نستخدم ProductAttribute::factory().
         */
        $attributeData = [
            [
                'name' => 'material',
                'display_name' => 'الخامة',
                'input_type' => 'select',
                'is_required' => true,
                'options' => ['خشب', 'معدن', 'زجاج'],
            ],
            [
                'name' => 'finish',
                'display_name' => 'نوع التشطيب',
                'input_type' => 'select',
                'is_required' => false,
                'options' => ['مطفي', 'لامع', 'طبيعي'],
            ],
            [
                'name' => 'length',
                'display_name' => 'الطول',
                'input_type' => 'number',
                'is_required' => false,
                'options' => null,
            ],
            [
                'name' => 'width',
                'display_name' => 'العرض',
                'input_type' => 'number',
                'is_required' => false,
                'options' => null,
            ],
            [
                'name' => 'height',
                'display_name' => 'الارتفاع',
                'input_type' => 'number',
                'is_required' => false,
                'options' => null,
            ],
            [
                'name' => 'custom_note',
                'display_name' => 'ملاحظة التخصيص',
                'input_type' => 'text',
                'is_required' => false,
                'options' => null,
            ],
        ];

        $this->attributes = collect();

        foreach ($attributeData as $data) {
            $this->attributes->push(
                ProductAttribute::query()->create([
                    'name' => $data['name'],
                    'display_name' => $data['display_name'],
                    'input_type' => $data['input_type'],
                    'is_required' => $data['is_required'],
                    'options' => $data['options'] === null
                        ? null
                        : json_encode(
                            $data['options'],
                            JSON_UNESCAPED_UNICODE
                        ),
                ])
            );
        }
    }

    private function seedCategoriesAndProducts(): void
    {
        $categoryNames = [
            'الأثاث المنزلي',
            'الطاولات',
            'الكراسي',
            'الديكور',
            'غرف النوم',
            'المكاتب',
            'الرفوف',
            'الهدايا',
        ];

        $rootCategories = collect();

        foreach ($categoryNames as $index => $name) {
            $rootCategories->push(
                ProductCategory::factory()->create([
                    'name' => $name,
                    'slug' => 'category-' . ($index + 1),
                ])
            );
        }

        $childNames = [
            'طاولات القهوة',
            'طاولات الطعام',
            'طاولات المكاتب',
            'كراسي الطعام',
            'كراسي المكاتب',
            'كراسي الاستقبال',
            'رفوف الحائط',
            'رفوف الكتب',
            'ديكور خشبي',
            'هدايا خشبية',
            'مستلزمات المكتب',
            'قطع مخصصة',
        ];

        $childCategories = collect();

        foreach ($childNames as $index => $name) {
            $childCategories->push(
                ProductCategory::factory()->create([
                    'name' => $name,
                    'slug' => 'subcategory-' . ($index + 1),
                    'parent_id' => $rootCategories->random()->id,
                ])
            );
        }

        $this->categories = $rootCategories->merge($childCategories);

        $productNames = [
            'طاولة خشبية يدوية',
            'طاولة قهوة عصرية',
            'طاولة جانبية خشبية',
            'طاولة طعام عائلية',
            'طاولة مكتب خشبية',
            'كرسي خشبي يدوي',
            'كرسي مكتب مريح',
            'كرسي طعام خشبي',
            'كرسي هزاز كلاسيكي',
            'كرسي استقبال فاخر',
            'رف حائط خشبي',
            'رف كتب أنيق',
            'رف ديكور منزلي',
            'خزانة خشبية صغيرة',
            'خزانة جانبية',
            'قطعة ديكور يدوية',
            'مزهرية خشبية',
            'صندوق هدايا خشبي',
            'صندوق تخزين أنيق',
            'لوحة ديكور خشبية',
            'مرآة بإطار خشبي',
            'ساعة حائط خشبية',
            'مصباح طاولة خشبي',
            'حامل نباتات خشبي',
            'حامل كتب مكتبي',
            'منظم مكتب خشبي',
            'طاولة تلفاز',
            'طاولة مدخل المنزل',
            'مقعد خشبي صغير',
            'مقعد تخزين عملي',
            'طاولة أطفال',
            'كرسي أطفال خشبي',
            'رف ألعاب للأطفال',
            'صندوق ألعاب خشبي',
            'طاولة قابلة للتخصيص',
            'كرسي قابل للتخصيص',
            'رف بتصميم خاص',
            'قطعة ديكور مخصصة',
            'صندوق هدايا فاخر',
            'طاولة اجتماعات',
            'مكتب إداري خشبي',
            'كرسي مدير فاخر',
            'رف ملفات مكتبي',
            'خزانة ملفات خشبية',
            'طاولة عمل احترافية',
            'حامل أدوات خشبي',
            'منظم مستلزمات مكتبية',
            'قطعة فنية خشبية',
            'تحفة خشبية يدوية',
        ];

        $this->products = collect();

        foreach ($productNames as $index => $name) {
            $this->products->push(
                Product::factory()
                    ->recycle($this->categories)
                    ->has(
                        ProductMedia::factory()->count(self::MEDIA_PER_PRODUCT),
                        'media'
                    )
                    ->create([
                        'name' => $name,
                        'slug' => 'product-' . ($index + 1),
                        'description' =>
                        'منتج عربي مصنوع بعناية وجودة عالية، مناسب للمنزل والمكتب ويمكن تخصيصه حسب الطلب.',
                    ])
            );
        }

        $this->products->each(function (Product $product) {
            $assignedColors = $this->colors->random(
                min(random_int(1, 4), $this->colors->count())
            );

            foreach ($assignedColors as $color) {
                ProductColor::factory()->create([
                    'product_id' => $product->id,
                    'color_id' => $color->id,
                ]);
            }

            $assignedAttributes = $this->attributes->random(
                min(random_int(2, 4), $this->attributes->count())
            );

            foreach ($assignedAttributes as $attribute) {
                ProductAttributeValue::factory()->create([
                    'product_id' => $product->id,
                    'attribute_id' => $attribute->id,
                ]);
            }
        });
    }

    private function seedFavoritesAndCarts(): void
    {
        $this->customers->each(function (Customer $customer) {
            $favoriteCount = random_int(0, 5);

            if ($favoriteCount === 0) {
                return;
            }

            $favorited = $this->products->random(
                min($favoriteCount, $this->products->count())
            );

            foreach ($favorited as $product) {
                Favorite::factory()->create([
                    'customer_id' => $customer->id,
                    'product_id' => $product->id,
                ]);
            }
        });

        $cartOwners = $this->customers->random(
            min(self::CARTS_COUNT, $this->customers->count())
        );

        $this->carts = $cartOwners->map(
            fn(Customer $customer) =>
            Cart::factory()->create([
                'customer_id' => $customer->id,
            ])
        );

        $this->carts->each(function (Cart $cart) {
            $items = $this->products->random(
                min(random_int(1, 4), $this->products->count())
            );

            foreach ($items as $product) {
                CartItem::factory()->create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                ]);
            }
        });
    }

    private function seedCustomizationSystem(): void
    {
        CustomDesignRequest::factory()
            ->count(self::CUSTOM_DESIGN_REQUESTS_COUNT)
            ->recycle($this->customers)
            ->create([
                'description' => 'أرغب في تنفيذ تصميم خاص حسب المقاسات المطلوبة.',
            ]);

        ProductCustomizationRequest::factory()
            ->count(self::PRODUCT_CUSTOMIZATION_REQUESTS_COUNT)
            ->recycle([
                $this->customers,
                $this->products,
                $this->colors,
                $this->designPatterns,
            ])
            ->create();
    }

    private function seedProductionStages(): void
    {
        $stageNames = [
            'استلام الطلب',
            'تجهيز المواد',
            'التصنيع',
            'التشطيب',
            'الفحص والتغليف',
        ];

        $this->productionStages = collect();

        foreach ($stageNames as $name) {
            $this->productionStages->push(
                OrderProductionStage::factory()->create([
                    'name' => $name,
                ])
            );
        }
    }

    private function seedOrders(): void
    {
        $addresses = $this->customers->flatMap(
            fn(Customer $customer) => $customer->addresses
        );

        $this->orders = Order::factory()
            ->count(self::ORDERS_COUNT)
            ->recycle([
                $this->customers,
                $addresses,
                $this->productionStages,
            ])
            ->create();

        $standardItems = OrderItem::factory()
            ->count(self::ORDER_ITEMS_COUNT)
            ->recycle([
                $this->orders,
                $this->products,
            ])
            ->create();

        $customers = Customer::all();
        $products = Product::all();
        $colors = Color::all();
        $designPatterns = DesignPattern::all();

        $customizedItems = OrderItem::factory()
            ->count(self::CUSTOMIZED_ORDER_ITEMS_COUNT)
            ->recycle([
                $this->orders,
                $this->products,
            ])
            ->state(function () use (
                $customers,
                $products,
                $colors,
                $designPatterns
            ) {
                return [
                    'is_customized' => true,
                    'product_customization_request_id' =>
                    ProductCustomizationRequest::factory()
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
            ->recycle([
                $this->orders,
                $this->adminUsers,
            ])
            ->create();

        $payableOrders = $this->orders->random(
            min(
                self::PAID_ORDERS_COUNT,
                $this->orders->count()
            )
        );

        foreach ($payableOrders as $order) {
            Payment::factory()->create([
                'order_id' => $order->id,
            ]);
        }

        OrderProductionStageHistory::factory()
            ->count(self::PRODUCTION_STAGE_HISTORY_COUNT)
            ->recycle([
                $this->orders,
                $this->productionStages,
                $this->adminUsers,
            ])
            ->create();
    }

    private function seedReviews(): void
    {
        $reviewTexts = [
            'منتج ممتاز وجودته عالية.',
            'الخامة جميلة والتصميم رائع.',
            'تجربة شراء ممتازة.',
            'المنتج مطابق للوصف.',
            'جودة ممتازة والتغليف جيد.',
            'أعجبني التصميم كثيراً.',
            'المنتج جميل جداً وأنصح به.',
            'الخدمة ممتازة وسرعة التنفيذ رائعة.',
        ];

        $orderCustomerMap = $this->orders
            ->pluck('customer_id', 'id');

        $reviewedItems = $this->orderItems->random(
            min(
                self::REVIEWED_ITEMS_COUNT,
                $this->orderItems->count()
            )
        );

        $reviews = $reviewedItems->map(
            fn(OrderItem $item) =>
            Review::factory()->create([
                'customer_id' =>
                $orderCustomerMap[$item->order_id],

                'product_id' =>
                $item->product_id,

                'order_item_id' =>
                $item->id,

                'comment' =>
                $reviewTexts[array_rand($reviewTexts)],
            ])
        );

        ReviewImage::factory()
            ->count(self::REVIEW_IMAGES_COUNT)
            ->recycle($reviews)
            ->create();
    }

    private function seedRawMaterials(): void
    {
        $materials = [
            'خشب الزان',
            'خشب البلوط',
            'خشب الصنوبر',
            'خشب الجوز',
            'خشب السنديان',
            'ألواح MDF',
            'ألواح خشبية طبيعية',
            'دهان أبيض',
            'دهان أسود',
            'دهان بني',
            'ورنيش لامع',
            'ورنيش مطفي',
            'مفصلات معدنية',
            'مسامير خشبية',
            'مواد تلميع',
        ];

        foreach ($materials as $name) {
            RawMaterial::factory()->create([
                'name' => $name,
            ]);
        }
    }
}
