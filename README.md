# Remonline-PHP-SDK
Remonline CRM SDK in PHP

## Installation

```bash
composer require gregorybiter/remonline-sdk
```

## Authentication

The SDK now uses **Bearer Token authentication** as per the updated RemOnline API.

1. Get your API key from **RemOnline Settings > API** section
2. Use it directly in the client initialization

## Rate Limits

- **3 requests per second** maximum
- HTTP 429 status code returned when rate limit exceeded

## Basic Usage

```php
require __DIR__ . '/vendor/autoload.php';

use Gbit\Remonline\RemonlineClient;
use Gbit\Remonline\Order;

// Initialize with your API key
$api = new RemonlineClient("your_api_key_here");

$order = new Order($api);
$orderRequest = $order->getOrders();
```

## Direct API Usage

You can also use the low-level API client directly:

```php
use Gbit\Remonline\Api;

$api = new Api('your_api_key_here');

// GET request example
$tasks = $api->api('tasks', ['page' => 1], 'GET');

// POST request example  
$newTask = [
    'title' => 'New Task',
    'description' => 'Created via API'
];
$result = $api->api('tasks', $newTask, 'POST');
```

## Error Handling

The SDK handles common API errors:

- **401 Unauthorized**: Invalid API key
- **429 Rate Limited**: Too many requests per second
- **cURL errors**: Network connectivity issues

```php
try {
    $result = $api->api('tasks', [], 'GET');
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

