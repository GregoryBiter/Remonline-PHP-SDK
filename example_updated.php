<?php

require_once 'vendor/autoload.php';

use Gbit\Remonline\RemonlineClient;
use Gbit\Remonline\Models\Order;
use Gbit\Remonline\Models\People;
use Gbit\Remonline\Models\Product;
use Gbit\Remonline\Models\Organization;
use Gbit\Remonline\Models\Estimate;
use Gbit\Remonline\Models\Assets;
use Gbit\Remonline\Models\Cashbox;
use Gbit\Remonline\Models\Setting;
use Gbit\Remonline\Models\Warehouse;

// Инициализация клиента
$api = new RemonlineClient('your-api-key-here');

try {
    // Работа с заказами
    $orders = new Order($api);
    
    // Получить все заказы
    $allOrders = $orders->get(['status' => 'in_progress']);
    echo "Найдено заказов: " . count($allOrders['data'] ?? $allOrders) . "\n";
    
    // Получить заказ по ID
    $order = $orders->getById(123);
    echo "Заказ #123: " . ($order['name'] ?? 'Без названия') . "\n";
    
    // Создать новый заказ
    $newOrder = $orders->create([
        'client_id' => 456,
        'branch_id' => 1,
        'order_type_id' => 1,
        'description' => 'Ремонт экрана'
    ]);
    echo "Создан заказ с ID: " . $newOrder['id'] . "\n";
    
    // Добавить комментарий
    $orders->addComment($newOrder['id'], 'Клиент оставил устройство', false);
    
    // Работа с клиентами
    $people = new People($api);
    $clients = $people->get(['search' => 'Иван']);
    echo "Найдено клиентов: " . count($clients['data'] ?? $clients) . "\n";
    
    // Создать нового клиента
    $newClient = $people->create([
        'first_name' => 'Иван',
        'last_name' => 'Петров',
        'phone' => '+380123456789'
    ]);
    echo "Создан клиент с ID: " . $newClient['id'] . "\n";
    
    // Работа с товарами
    $products = new Product($api);
    $productList = $products->get(['search' => 'экран']);
    echo "Найдено товаров: " . count($productList['data'] ?? $productList) . "\n";
    
    // Работа с организациями
    $orgs = new Organization($api);
    $organizations = $orgs->get();
    echo "Организаций: " . count($organizations['data'] ?? $organizations) . "\n";
    
    // Работа со сметами
    $estimates = new Estimate($api);
    $estimateList = $estimates->get();
    echo "Смет: " . count($estimateList['data'] ?? $estimateList) . "\n";
    
    // Настройки
    $settings = new Setting($api);
    $branches = $settings->getLocations();
    echo "Филиалов: " . count($branches['data'] ?? $branches) . "\n";
    
    $employees = $settings->getEmployees();
    echo "Сотрудников: " . count($employees['data'] ?? $employees) . "\n";
    
    // Склад
    $warehouse = new Warehouse($api);
    $warehouses = $warehouse->get();
    echo "Складов: " . count($warehouses['data'] ?? $warehouses) . "\n";
    
    // Касса
    $cashbox = new Cashbox($api);
    $cashboxes = $cashbox->get();
    echo "Касс: " . count($cashboxes['data'] ?? $cashboxes) . "\n";
    
    // Активы
    $assets = new Assets($api);
    $assetList = $assets->get();
    echo "Активов: " . count($assetList['data'] ?? $assetList) . "\n";
    
    echo "\n=== Все операции выполнены успешно! ===\n";
    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    echo "Тип: " . get_class($e) . "\n";
    
    if (method_exists($e, 'getUserFriendlyMessage')) {
        echo "Дружественное сообщение: " . $e->getUserFriendlyMessage() . "\n";
    }
    
    if (method_exists($e, 'isValidationError') && $e->isValidationError()) {
        echo "Ошибки валидации:\n";
        foreach ($e->getValidationErrors() as $field => $errors) {
            echo "  $field: " . implode(', ', (array)$errors) . "\n";
        }
    }
}
