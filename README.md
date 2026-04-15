# Roapp-PHP-SDK

PHP SDK для работы с API Roapp CRM - системы управления ремонтом и сервисным обслуживанием.

## 📦 Установка

```bash
composer require gregorybiter/roapp-sdk
```

**Требования:**
- PHP >= 7.4
- cURL extension

## 🔑 Аутентификация

SDK использует **Bearer Token** для аутентификации согласно обновлённому API Roapp.

1. Получите API ключ в **Roapp > Настройки > API**
2. Используйте его при инициализации клиента

## ⚡ Быстрый старт

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use Gbit\Roapp\RoappClient;
use Gbit\Roapp\Models\Order;
use Gbit\Roapp\Models\People;
use Gbit\Roapp\Models\Product;

// Инициализация клиента
$api = new RoappClient("your_api_key_here");

// Работа с заказами
$orderModel = new Order($api);
$orders = $orderModel->get(['page' => 1]);

// Работа с клиентами
$peopleModel = new People($api);
$clients = $peopleModel->get();

// Работа с товарами
$productModel = new Product($api);
$products = $productModel->get();
```

## 📊 Ограничения API

- **3 запроса в секунду** максимум
- При превышении лимита возвращается HTTP статус **429**
- Используйте метод `requestWithRetry()` для автоматических повторных попыток

## 🔥 Обработка ошибок

SDK предоставляет расширенную обработку ошибок через `RoappApiException`:

```php
use Gbit\Roapp\RoappApiException;

try {
    $order = $orderModel->getById(12345);
} catch (RoappApiException $e) {
    // Получить HTTP код ошибки
    echo "HTTP Code: " . $e->getHttpCode() . "\n";
    
    // Получить детали ошибки
    print_r($e->getErrorDetails());
    
    // Получить пользовательское сообщение на русском
    echo $e->getUserFriendlyMessage() . "\n";
    
    // Проверить тип ошибки
    if ($e->isValidationError()) {
        echo "Ошибка валидации: " . $e->getValidationErrorsMessage();
    }
    
    if ($e->isRateLimitError()) {
        echo "Превышен лимит запросов";
    }
    
    if ($e->isAuthenticationError()) {
        echo "Неверный API ключ";
    }
}
```

### Типы проверок ошибок

```php
$e->isValidationError()        // HTTP 400 или 422
$e->isAuthenticationError()    // HTTP 401
$e->isAuthorizationError()     // HTTP 403
$e->isNotFoundError()          // HTTP 404
$e->isRateLimitError()         // HTTP 429

// Получить ошибки валидации конкретного поля
$errors = $e->getFieldErrors('client_id');
```

## 🛠️ Прямое использование API

Для низкоуровневых запросов используйте класс `Api`:

```php
use Gbit\Roapp\Api;

$api = new Api('your_api_key_here');

// GET запрос
$response = $api->api('orders', ['page' => 1, 'status' => 'new'], 'GET');

// POST запрос
$data = [
    'title' => 'Новая задача',
    'description' => 'Создана через API'
];
$response = $api->api('tasks', $data, 'POST');

// PATCH запрос
$response = $api->api('orders/123', ['status_id' => 5], 'PATCH');

// DELETE запрос
$response = $api->api('tasks/456', [], 'DELETE');
```

## 📚 Доступные модели и методы

### 🛒 Orders (Заказы)

```php
use Gbit\Roapp\Models\Order;

$order = new Order($api);

// Получить список заказов
$orders = $order->get(['page' => 1, 'status' => 'new']);

// Получить заказ по ID
$order->getById(123);

// Получить статусы заказов
$order->getStatuses();

// Создать заказ
$order->create([
    'branch_id' => 1,
    'order_type_id' => 2,
    'client_id' => 100,
    'description' => 'Ремонт ноутбука'
]);

// Обновить заказ
$order->update(123, ['status_id' => 5]);

// Получить позиции заказа
$order->getItems(123);

// Добавить позицию в заказ
$order->addItem(123, ['product_id' => 50, 'quantity' => 2]);

// Удалить заказ
$order->delete(123);
```

### 👥 People (Клиенты)

```php
use Gbit\Roapp\Models\People;

$people = new People($api);

// Получить список клиентов
$people->get(['page' => 1, 'search' => 'Иван']);

// Получить клиента по ID
$people->getByID(100);

// Получить организацию клиента
$people->getOrganization(100);

// Создать клиента
$people->create([
    'name' => 'Иван Петров',
    'phone' => '+380501234567',
    'email' => 'ivan@example.com'
]);

// Обновить клиента
$people->update(100, ['email' => 'new@example.com']);

// Удалить клиента
$people->delete(100);

// Добавить комментарий
$people->addComment(100, 'Постоянный клиент');

// Объединить клиентов
$people->merge(100, [101, 102]);
```

### 📦 Products (Товары)

```php
use Gbit\Roapp\Models\Product;

$product = new Product($api);

// Получить список товаров
$product->get(['page' => 1, 'category_id' => 5]);

// Получить товар по ID
$product->getByID(50);

// Получить категории товаров
$product->getCategories();

// Создать товар
$product->create([
    'title' => 'Матрица для ноутбука',
    'price' => 2500,
    'category_id' => 5
]);

// Обновить товар
$product->update(50, ['price' => 2700]);

// Удалить товар
$product->delete(50);
```

### 🛠️ Services (Услуги)

```php
use Gbit\Roapp\Models\Service;

$service = new Service($api);

// Получить список услуг
$service->get(['page' => 1]);

// Получить услугу по ID
$service->getByID(10);

// Получить категории услуг
$service->getCategories();

// Поиск услуг
$service->search('чистка');

// Создать услугу
$service->create([
    'title' => 'Чистка от пыли',
    'price' => 500
]);

// Обновить услугу
$service->update(10, ['price' => 600]);

// Получить цены на услугу
$service->getPrices(10);

// Удалить услугу
$service->delete(10);
```

### ✅ Tasks (Задачи)

```php
use Gbit\Roapp\Models\Task;

$task = new Task($api);

// Получить список задач
$task->get(['page' => 1, 'status' => 'pending']);

// Получить задачу по ID
$task->getByID(200);

// Создать задачу
$task->create([
    'title' => 'Позвонить клиенту',
    'description' => 'Уточнить детали заказа',
    'due_date' => '2026-01-20'
]);

// Обновить задачу
$task->update(200, ['status' => 'in_progress']);

// Завершить задачу
$task->complete(200);

// Добавить комментарий к задаче
$task->addComment(200, 'Клиент не отвечает');

// Удалить задачу
$task->delete(200);
```

### 📄 Invoices (Счета)

```php
use Gbit\Roapp\Models\Invoice;

$invoice = new Invoice($api);

// Получить список счетов
$invoice->get(['page' => 1]);

// Получить счёт по ID
$invoice->getByID(300);

// Получить статусы счетов
$invoice->getStatuses();

// Создать счёт
$invoice->create([
    'client_id' => 100,
    'branch_id' => 1,
    'items' => [...]
]);

// Обновить счёт
$invoice->update(300, ['comment' => 'Срочный']);

// Установить статус счёта
$invoice->setStatus(300, 5, 'Оплачено');

// Получить позиции счёта
$invoice->getItems(300);

// Добавить позицию в счёт
$invoice->addItem(300, ['product_id' => 50, 'quantity' => 1]);

// Обновить позицию счёта
$invoice->updateItem(300, 1, ['quantity' => 2]);

// Отправить счёт
$invoice->send(300, ['email' => 'client@example.com']);

// Отметить как оплаченный
$invoice->markAsPaid(300, ['amount' => 5000]);

// Удалить счёт
$invoice->delete(300);
```

### 💰 Sales (Продажи)

```php
use Gbit\Roapp\Models\Sale;

$sale = new Sale($api);

// Получить список продаж
$sale->get(['page' => 1]);

// Получить продажу по ID
$sale->getByID(400);

// Создать продажу
$sale->create([
    'client_id' => 100,
    'branch_id' => 1,
    'items' => [...]
]);

// Обновить продажу
$sale->update(400, ['comment' => 'Скидка 10%']);

// Получить позиции продажи
$sale->getItems(400);

// Добавить позицию
$sale->addItem(400, ['product_id' => 50, 'quantity' => 1]);

// Обновить позицию
$sale->updateItem(400, 1, ['quantity' => 2]);

// Удалить позицию
$sale->deleteItem(400, 1);

// Завершить продажу
$sale->complete(400);

// Возврат
$sale->refund(400, ['amount' => 1000]);

// Удалить продажу
$sale->delete(400);
```

### 👤 Users (Пользователи)

```php
use Gbit\Roapp\Models\User;

$user = new User($api);

// Получить список пользователей
$user->get();

// Получить пользователя по ID
$user->getByID(5);

// Получить текущего пользователя
$user->getCurrentUser();

// Создать пользователя
$user->create([
    'name' => 'Новый сотрудник',
    'email' => 'employee@example.com',
    'role_id' => 3
]);

// Обновить пользователя
$user->update(5, ['phone' => '+380501234567']);

// Обновить профиль
$user->updateProfile(['phone' => '+380501234567']);

// Получить разрешения
$user->getPermissions(5);

// Установить разрешения
$user->setPermissions(5, ['can_edit_orders' => true]);

// Получить роли
$user->getRoles();

// Назначить роль
$user->assignRole(5, 2);

// Удалить роль
$user->removeRole(5, 2);

// Удалить пользователя
$user->delete(5);
```

### 🏭 Warehouse (Склад)

```php
use Gbit\Roapp\Models\Warehouse;

$warehouse = new Warehouse($api);

// Получить список складов
$warehouse->get();

// Получить склад по филиалу
$warehouse->get(1);

// Получить товары на складе
$warehouse->goods(1, [5, 10], true, 1);

// Получить категории склада
$warehouse->getCategories();

// Получить оприходования
$warehouse->getPostings(1, '2026-01-01', [100, 101]);
```

## 🔄 Пагинация

Большинство методов поддерживают пагинацию через параметр `page`:

```php
// Получить первую страницу
$orders = $orderModel->get(['page' => 1]);

// Получить все страницы автоматически
$allOrders = $api->getData('orders', [], true);
```

## 🔁 Автоматические повторные попытки

Для запросов с автоматическими повторными попытками при ошибке 429:

```php
$response = $api->requestWithRetry(
    'orders',           // endpoint
    ['page' => 1],      // параметры
    'GET',              // метод
    '',                 // модель (опционально)
    3,                  // максимум попыток
    1                   // задержка в секундах
);
```

## 📝 Логирование

SDK автоматически логирует ошибки в `logs/error.log`:

```php
use Gbit\Roapp\Api;

// Записать свой лог
Api::push_logs('Произошло событие', false); // false = warning, true = error
```

## 📖 Дополнительная документация

- [PAGINATION.md](docs/PAGINATION.md) - Подробности работы с пагинацией
- [MIGRATION_GUIDE.php](MIGRATION_GUIDE.php) - Руководство по миграции
- [Официальная документация API Roapp](https://api.remonline.app/docs)

## 🤝 Поддержка

- **Issues**: [GitHub Issues](https://github.com/GregoryBiter/Roapp-PHP-SDK/issues)
- **Email**: gregorybiter@gmail.com

## 📄 Лицензия

MIT License. См. [LICENSE](LICENSE) для деталей.

## 🔧 Примеры

Смотрите примеры использования в файлах:
- [example_simple.php](example_simple.php)
- [example_updated.php](example_updated.php)
- [example_error_handling.php](example_error_handling.php)

