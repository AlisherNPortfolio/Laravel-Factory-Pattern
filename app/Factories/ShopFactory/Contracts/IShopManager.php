<?php

namespace App\Factories\ShopFactory\Contracts;

use App\Services\Shop\Contracts\IShopService;

interface IShopManager
{
    public function make($name): IShopService;
}
