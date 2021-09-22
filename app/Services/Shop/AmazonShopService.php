<?php

namespace App\Services\Shop;

use App\Services\Shop\Contracts\IShopService;

class AmazonShopService implements IShopService
{
    private $config;

    public function getProducts(): array
    {
        return [
            'Amazon Product #1',
            'Amazon Product #2',
            'Amazon Product #3',
        ];
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }
}
