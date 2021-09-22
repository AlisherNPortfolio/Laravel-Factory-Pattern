<?php

namespace Tests\Unit;

use App\Factories\ShopFactory\Contracts\IShopManager;
use Tests\TestCase;

class ShopManagerTest extends TestCase
{
    public function test_can_use_ebay_service()
    {
        $factory = app(IShopManager::class);
        $service = $factory->make('ebay');
        $products = $service->getProducts();
        dump($products);
        self::assertEquals([
            'Ebay Product #1',
            'Ebay Product #2',
            'Ebay Product #3',
        ], $products);
    }

    public function test_can_use_amazon_service()
    {
        $factory = app(IShopManager::class);
        $service = $factory->make('amazon');
        $products = $service->getProducts();
        dump($products);
        self::assertEquals([
            'Amazon Product #1',
            'Amazon Product #2',
            'Amazon Product #3',
        ], $products);
    }
}
