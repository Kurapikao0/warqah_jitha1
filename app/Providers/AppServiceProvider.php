<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
