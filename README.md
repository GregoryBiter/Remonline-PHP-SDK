# Remonline-PHP-SDK
SDK для CRM системы Remonline
## Как запустить?
    ```csharp
composer install
php -S localhost:8080
    ```
## Как использовать?
    ```csharp
require __DIR__ . '/vendor/autoload.php';
use GBIT\Remonline\Api;
use GBIT\Remonline\Models\Order;
$reapi = new Api("your api key");
$Order = new Order($api);
$echo['Order'] = $Order->page(1)->get();
$echo['CustomFields']  = $Order->getCustomFields();
    ```
