<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Customer;
use App\Policies\CustomerPolicy;
use App\Repositories\CustomizationRepository;
use App\Repositories\Contracts\CustomizationRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\CartRepository;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\FavoriteRepository;
use App\Repositories\Contracts\FavoriteRepositoryInterface;
use App\Repositories\PaymentRepository;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\OrderStatusHistoryRepository;
use App\Repositories\Contracts\OrderStatusHistoryRepositoryInterface;
use App\Repositories\OrderProductionStageRepository;
use App\Repositories\Contracts\OrderProductionStageRepositoryInterface;
use App\Repositories\DesignPatternRepository;
use App\Repositories\Contracts\DesignPatternRepositoryInterface;
use App\Repositories\ProductMediaRepository;
use App\Repositories\Contracts\ProductMediaRepositoryInterface;
use App\Repositories\ColorRepository;
use App\Repositories\Contracts\ColorRepositoryInterface;
use App\Repositories\ProductAttributeRepository;
use App\Repositories\Contracts\ProductAttributeRepositoryInterface;
use App\Repositories\ProductAttributeValueRepository;
use App\Repositories\Contracts\ProductAttributeValueRepositoryInterface;
use App\Repositories\OrderProductionRepository;
use App\Repositories\Contracts\OrderProductionRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\CustomerRepository;
use \App\Repositories\Contracts\AdminReviewRepositoryInterface;
use \App\Repositories\AdminReviewRepository;
use \App\Repositories\Contracts\AuthRepositoryInterface;
use \App\Repositories\AuthRepository;
use App\Models\Address;
use App\Policies\AddressPolicy;
use App\Repositories\Contracts\AddressRepositoryInterface;
use App\Repositories\AddressRepository;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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

        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);      

        
        $this->app->bind(

            AddressRepositoryInterface::class,

            AddressRepository::class

        );

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(
            Customer::class,
            CustomerPolicy::class
        ); 

        // Gate::policy(
        //     Category::class,
        //     CategoryPolicy::class
        // );      
        
        Gate::policy(
            Address::class,
            AddressPolicy::class
        );        
    }
}
