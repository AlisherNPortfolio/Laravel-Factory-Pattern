<?php

namespace App\Services\Shop;

use App\Services\Shop\Contracts\IShopService;

class EbayShopService implements IShopService
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getProducts(): array
    {
        return [
            'Ebay Product #1',
            'Ebay Product #2',
            'Ebay Product #3',
        ];
    }
}
