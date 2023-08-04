# Remonline-PHP-SDK
Remonline CRM SDK in PHP
## How to start?

```php 
composer install
php -S localhost:8080
```
## How to use?
```php 
require __DIR__ . '/vendor/autoload.php';

use Gbit\Remonline\Models\Order;

$order = new Order("your api key");
echo $order->filter(['sort_dir' => 'asc'])->get();
echo $order->getCustomFields();
```

