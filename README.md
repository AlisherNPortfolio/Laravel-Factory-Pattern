# Laravel Factory Pattern

Tasavvur qiling, Laravel proyekt qilishda bir xildagi metod va xususiyatlarga ega bo'lgan obyektlar bilan ishlayapsiz va bu obyektlarni dinamik ko'rinishda yaratishingiz kerak bo'lsin. Bunda, albatta, Factory Pattern sizga yordam beradi. Laravel bu pattern-dan drayver asosida ishlaydigan komponentlarda `app('cache')->store('redis')` kabi metodlarni chaqirish paytida foydalanadi. 

### Misol

Factory pattern-ni tushuntirish uchun Amazon va Ebay-dan mahsulotlarni olib beradigan obyektlarni yaratuvchi misolni ko'ramiz.

Birinchi bo'lib, bizga bir xil tipdagi obyektni qaytaruvchi factory klasi kerak bo'ladi. Shu sababli, bu klas uchun `app\Services\Shop\Contracts` papkasida `IShopService` interfeysini yozamiz:

```bash
interface IShopService
{
    public function getProducts(): array;
}
```

Keyin, bu interfeysni Amazon va Ebay uchun yaratilgan klaslar uchun ishlatamiz:

`app\Services\Shop\EbayShopService.php`:

```bash
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
```

`app\Services\Shop\AmazonShopService.php`:

```bash
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
```

###### Misolda Factory-ni qo'llash

Hozir factory uchun interfeys ochamiz. Keyinchalik uni factory klasiga bog'lab qo'yamiz. Undan so'ng, interfeysni xohlagan joyda inject qilishimiz mumkin bo'ladi.

`app\Factories\ShopFactory\Contracts\IShopManager.php`:

```bash
interface IShopManager
{
    public function make($name): IShopService;
}
```

`app\Factories\ShopFactory\ShopManager.php`:

```bash
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

        // Har safar service-ni yaratib o`tirmaslik uchun
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
```

Yuqoridagi klasdagi `ShopManager`-ning `make` metodida `shops` array-ini tekshirib ko'ramiz, agar unda berilgan obyekt mavjud bo'lsa, shu obyektning o'zini qaytarib beramiz. Aks holda, berilgan `$name` bo'yicha kerakli metoddan yangi servisni olib, avval uni `shops` array-iga joylashtirib keyin yangi servisni qaytarib beramiz.

###### Factory-ni container ro'yxatiga qo'shamiz

Buning uchun yoki `AppServiceProvider`-da yoki yangi service provider ochib factory-ni ro'yxatga olish mumkin. Agar, factory klaslar soni ko'p bo'ladigan bo'lsa, alohida `FactoryServiceProvider` nomli service provider ochgan yaxshi (`php artisan make:provider FactoryServiceProvider`):

```bash
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
```

Service provider-ni `config/app.php`  ro'yxatga olish:
```bash
//...
'providers' => [
        //...
        App\Providers\FactoryServiceProvider::class,
        //...
    ],
//...
```


### Testlash

Testlash uchun Unit testdan foydalanamiz. Test-ni yaratish: `php artisan make:test ShopManagerTest --unit`

`tests\Unit\ShopManagerTest.php`:

```bash
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
```

Test-ni ishga tushirish: `php artisan test`

Agar

> Warning: TTY mode is not supported on Windows platform.

muammosi chiqsa `php artisan test --without-tty` ko'rinishida ishlatish mumkin.

**Test natijasi:**

```bash
array:3 [
  0 => "Ebay Product #1"
  1 => "Ebay Product #2"
  2 => "Ebay Product #3"
]
array:3 [
  0 => "Amazon Product #1"
  1 => "Amazon Product #2"
  2 => "Amazon Product #3"
]

   PASS  Tests\Unit\ShopManagerTest
  ✓ can use ebay service
  ✓ can use amazon service

  Tests:  2 passed
  Time:   0.23s
```
