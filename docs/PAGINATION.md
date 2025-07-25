# Пагинация в Remonline PHP SDK

## Обзор

SDK предоставляет удобные методы для работы с пагинацией данных из API RemOnline.

## Методы пагинации

### 1. Пагинация по страницам

```php
use Gbit\Remonline\RemonlineClient;
use Gbit\Remonline\Models\Order;

$client = new RemonlineClient('your-api-key');
$orders = new Order($client);

// Получить первую страницу (по умолчанию)
$result = $orders->get();

// Получить конкретную страницу
$result = $orders->page(2)->get();

// Получить страницу с указанным размером
$result = $orders->page(3)->pageSize(20)->get();
```

### 2. Лимит и смещение

```php
// Получить первые 10 записей
$result = $orders->limit(10)->get();

// Получить 20 записей, начиная с 50-й
$result = $orders->offset(50)->limit(20)->get();
```

### 3. Цепочка методов

```php
// Комбинирование параметров пагинации
$result = $orders
    ->page(2)
    ->pageSize(25)
    ->get(['status' => 'active']);

// Использование с фильтрами
$result = $orders
    ->limit(50)
    ->get([
        'branch_id' => 123,
        'created_at' => ['2024-01-01', '2024-12-31']
    ]);
```

### 4. Получение всех страниц

```php
// Автоматически получить все страницы
$allOrders = $orders->get([], true);
```

## Информация о пагинации

После выполнения запроса можно получить метаинформацию:

```php
$result = $orders->page(2)->get();

// Получить всю метаинформацию
$meta = $orders->meta();
/*
Array (
    [count] => 1000
    [page] => 2
    [page_size] => 50
    [total_pages] => 20
)
*/

// Получить конкретное значение
$totalCount = $orders->meta('count');
$currentPage = $orders->meta('page');
$totalPages = $orders->meta('total_pages');
```

## Примеры использования

### Простая пагинация

```php
$client = new RemonlineClient('your-api-key');
$orders = new Order($client);

// Получить первую страницу
$page1 = $orders->get();
echo "Всего заказов: " . $orders->meta('count') . "\n";
echo "Страниц: " . $orders->meta('total_pages') . "\n";

// Получить следующую страницу
$page2 = $orders->page(2)->get();
```

### Итерация по страницам

```php
$orders = new Order($client);
$currentPage = 1;

do {
    $result = $orders->page($currentPage)->get();
    $data = $result;
    
    // Обработка данных текущей страницы
    foreach ($data as $order) {
        echo "Order ID: " . $order['id'] . "\n";
    }
    
    $currentPage++;
    $totalPages = $orders->meta('total_pages');
    
} while ($currentPage <= $totalPages);
```

### Пагинация с кастомным размером страницы

```php
$orders = new Order($client);

// Получить большие страницы по 100 элементов
$result = $orders->pageSize(100)->get();

// Или использовать лимит (то же самое)
$result = $orders->limit(100)->get();
```

### Использование offset для "бесконечной прокрутки"

```php
$orders = new Order($client);
$offset = 0;
$limit = 20;

while (true) {
    $result = $orders->offset($offset)->limit($limit)->get();
    
    if (empty($result)) {
        break; // Больше нет данных
    }
    
    // Обработка данных
    foreach ($result as $order) {
        // ...
    }
    
    $offset += $limit;
}
```

## Важные замечания

1. **Автосброс параметров**: После каждого запроса параметры пагинации (page, pageSize, offset) автоматически сбрасываются.

2. **Fluent Interface**: Все методы пагинации возвращают объект модели, что позволяет создавать цепочки вызовов.

3. **Совместимость с фильтрами**: Пагинация работает совместно с любыми фильтрами API.

4. **Производительность**: При использовании `getAllPage = true` будут выполнены все необходимые запросы для получения всех данных. Используйте осторожно с большими наборами данных.
