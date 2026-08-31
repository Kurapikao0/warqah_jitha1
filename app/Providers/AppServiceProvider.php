<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\RawMaterial;
use App\Models\Role;
use App\Policies\AddressPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RawMaterialPolicy;
use App\Policies\RolePolicy;
use App\Repositories\AddressRepository;
use App\Repositories\AdminReviewRepository;
use App\Repositories\AuthRepository;
use App\Repositories\CartRepository;
use App\Repositories\ColorRepository;
use App\Repositories\Contracts\AddressRepositoryInterface;
use App\Repositories\Contracts\AdminReviewRepositoryInterface;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\ColorRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\CustomizationRepositoryInterface;
use App\Repositories\Contracts\DesignPatternRepositoryInterface;
use App\Repositories\Contracts\EmailLogRepositoryInterface;
use App\Repositories\Contracts\FavoriteRepositoryInterface;
use App\Repositories\Contracts\OrderProductionRepositoryInterface;
use App\Repositories\Contracts\OrderProductionStageRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\OrderStatusHistoryRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\ProductAttributeRepositoryInterface;
use App\Repositories\Contracts\ProductAttributeValueRepositoryInterface;
use App\Repositories\Contracts\ProductMediaRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\CustomizationRepository;
use App\Repositories\DesignPatternRepository;
use App\Repositories\EmailLogRepository;
use App\Repositories\FavoriteRepository;
use App\Repositories\OrderProductionRepository;
use App\Repositories\OrderProductionStageRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderStatusHistoryRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProductAttributeRepository;
use App\Repositories\ProductAttributeValueRepository;
use App\Repositories\ProductMediaRepository;
use App\Repositories\ProductRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\CustomDesignRequestImageRepositoryInterface;
use App\Repositories\CustomDesignRequestImageRepository;
use App\Repositories\Contracts\CustomDesignRequestRepositoryInterface;
use App\Repositories\CustomDesignRequestRepository;
use App\Models\ProductCustomizationRequest;
use App\Policies\CustomizationPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        $this->app->bind(
            CustomizationRepositoryInterface::class,
            CustomizationRepository::class
        );

        $this->app->bind(
            OrderRepositoryInterface::class,
            OrderRepository::class
        );

        $this->app->bind(
            CartRepositoryInterface::class,
            CartRepository::class
        );

        $this->app->bind(
            FavoriteRepositoryInterface::class,
            FavoriteRepository::class
        );

        $this->app->bind(
            PaymentRepositoryInterface::class,
            PaymentRepository::class
        );

        $this->app->bind(
            OrderStatusHistoryRepositoryInterface::class,
            OrderStatusHistoryRepository::class
        );

        $this->app->bind(
            OrderProductionStageRepositoryInterface::class,
            OrderProductionStageRepository::class
        );

        $this->app->bind(
            OrderProductionRepositoryInterface::class,
            OrderProductionRepository::class
        );

        $this->app->bind(
            DesignPatternRepositoryInterface::class,
            DesignPatternRepository::class
        );

        $this->app->bind(
            ProductMediaRepositoryInterface::class,
            ProductMediaRepository::class
        );

        $this->app->bind(
            CustomDesignRequestImageRepositoryInterface::class,
            CustomDesignRequestImageRepository::class
        );

        $this->app->bind(
            ColorRepositoryInterface::class,
            ColorRepository::class
        );

        $this->app->bind(
            ProductAttributeRepositoryInterface::class,
            ProductAttributeRepository::class
        );

        $this->app->bind(
            ProductAttributeValueRepositoryInterface::class,
            ProductAttributeValueRepository::class
        );

        $this->app->bind(
            CustomerRepositoryInterface::class,
            CustomerRepository::class
        );

        $this->app->bind(
            AdminReviewRepositoryInterface::class,
            AdminReviewRepository::class
        );

        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class
        );

        $this->app->bind(
            AddressRepositoryInterface::class,
            AddressRepository::class
        );

        $this->app->bind(
            EmailLogRepositoryInterface::class,
            EmailLogRepository::class
        );

        $this->app->bind(
            CustomDesignRequestRepositoryInterface::class,
            CustomDesignRequestRepository::class
        );
    }

    public function boot(): void
    {
        Gate::policy(
            Customer::class,
            CustomerPolicy::class
        );

        Gate::policy(
            Permission::class,
            PermissionPolicy::class
        );

        Gate::policy(
            Role::class,
            RolePolicy::class
        );

        Gate::policy(
            RawMaterial::class,
            RawMaterialPolicy::class
        );

        Gate::policy(
            ProductCustomizationRequest::class,
            CustomizationPolicy::class
        );

        Gate::policy(
            Address::class,
            AddressPolicy::class
        );

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
