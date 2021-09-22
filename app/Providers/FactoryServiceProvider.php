<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FactoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Factories\ShopFactory\Contracts\IShopManager::class, function ($app) {
            return new \App\Factories\ShopFactory\ShopManager($app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
