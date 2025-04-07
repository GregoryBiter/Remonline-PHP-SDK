# Remonline-PHP-SDK
Remonline CRM SDK in PHP
## How to start?

```php 
composer require gregorybiter/remonline-sdk
```
## How to use?
```php 
require __DIR__ . '/vendor/autoload.php';

use Gbit\Remonline\RemonlineClient;
use Gbit\Remonline\Order;

$api = new RemonlineClient("api_key");

$order = new Order($api);

$orderRequest = $order->getOrders();

```

