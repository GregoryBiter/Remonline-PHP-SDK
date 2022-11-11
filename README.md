# Remonline-PHP-SDK
Remonline CRM SDK in PHP
## How to start?

```php 
composer install
php -S localhost:8080
```
## How to use?
```php require __DIR__ . '/vendor/autoload.php';

use GBIT\Remonline\Api;
use GBIT\Remonline\Models\Order;

$api = new Api("your api key");
$Order = new Order($api);
$echo['Order'] = $Order->page(1)->get();
$echo['CustomFields']  = $Order->getCustomFields();
```
