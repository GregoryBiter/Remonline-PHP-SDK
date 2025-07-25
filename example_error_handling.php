<?php

require_once 'vendor/autoload.php';

use Gbit\Remonline\RemonlineClient;
use Gbit\Remonline\RemonlineApiException;

// Пример использования с обработкой ошибок
try {
    $client = new RemonlineClient('your-api-key-here');
    
    // Выполняем запрос
    $response = $client->request('orders', [], 'GET');
    
    echo "Успешный ответ: " . json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (RemonlineApiException $e) {
    echo "Ошибка API RemOnline:\n";
    echo "Код ошибки: " . $e->getHttpCode() . "\n";
    echo "Сообщение: " . $e->getMessage() . "\n";
    echo "Понятное сообщение: " . $e->getUserFriendlyMessage() . "\n";
    echo "URL запроса: " . $e->getApiUrl() . "\n";
    
    // Получаем детали ошибки из ответа API
    $errorDetails = $e->getErrorDetails();
    if (!empty($errorDetails)) {
        echo "Детали ошибки от API: " . json_encode($errorDetails, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    }
    
    // Проверяем тип ошибки
    if ($e->isAuthenticationError()) {
        echo "Проблема с аутентификацией - проверьте API ключ\n";
    } elseif ($e->isRateLimitError()) {
        echo "Превышен лимит запросов - попробуйте позже\n";
    } elseif ($e->isValidationError()) {
        echo "Ошибка валидации данных\n";
        // Можно вывести конкретные поля с ошибками
        if (isset($errorDetails['errors'])) {
            foreach ($errorDetails['errors'] as $field => $errors) {
                echo "Поле '$field': " . implode(', ', $errors) . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Общая ошибка: " . $e->getMessage() . "\n";
}

// Пример с автоматическими повторами при rate limit
try {
    $client = new RemonlineClient('your-api-key-here');
    
    // Запрос с автоматическими повторами
    $response = $client->requestWithRetry('orders', [], 'GET', '', 3, 2);
    
    echo "Ответ получен (возможно, после повторов): " . json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (RemonlineApiException $e) {
    echo "Не удалось выполнить запрос даже с повторами: " . $e->getUserFriendlyMessage() . "\n";
}
