<?php

namespace App\Factories\ShopFactory;

use App\Factories\ShopFactory\Contracts\IShopManager;
use App\Services\Shop\AmazonShopService;
use App\Services\Shop\Contracts\IShopService;
use App\Services\Shop\EbayShopService;
use Illuminate\Support\Arr;

class ShopManager implements IShopManager
{
    private $shops = [];

    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function make($name): IShopService
    {
        $service = Arr::get($this->shops, $name);

        // Har safar service-ni yaratib o'tirmaslik uchun
        if ($service) {
            return $service;
        }

        $createdMethod = 'create' . ucfirst($name) . 'ShopService';

        if (!method_exists($this, $createdMethod)) {
            throw new \Exception("Shop $name is not supported!");
        }

        $service = $this->{$createdMethod}();

        $this->shops[$name] = $service;

        return $service;
    }

    public function createEbayShopService(): EbayShopService
    {
        $config = $this->app['config']['shops.ebay'];
        return new EbayShopService($config);
    }

    public function createAmazonShopService(): AmazonShopService
    {
        $service = new AmazonShopService();
        $config = $this->app['config']['shops.amazon'];
        $service->setConfig($config);

        return $service;
    }
}
