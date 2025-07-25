<?php

namespace Gbit\Remonline;

use Exception;

/**
 * RemonlineClient - High-level client for RemOnline API
 * 
 * This client provides convenient methods for common operations
 * and uses the updated Bearer Token authentication.
 */
class RemonlineClient
{
    const POST = 'POST';
    const GET = 'GET';
    const PUT = 'PUT';

    protected $api;

    /**
     * @param string $apiKey API key from RemOnline Settings > API section
     */
    public function __construct(string $apiKey)
    {
        $this->api = new Api($apiKey);
    }

    /**
     * Make API request with enhanced error handling
     * 
     * @param string $url
     * @param array $params
     * @param string $type
     * @param string $model
     * @return array
     * @throws RemonlineApiException On API errors
     */
    public function request(string $url, array $params, string $type, string $model = ''): array
    {
        try {
            return $this->api->api($url, $params, $type, $model);
        } catch (RemonlineApiException $e) {
            // Log the error with additional context
            $this->pushLogs([
                'error_type' => 'RemonlineApiException',
                'message' => $e->getMessage(),
                'http_code' => $e->getHttpCode(),
                'error_details' => $e->getErrorDetails(),
                'api_url' => $e->getApiUrl(),
                'request_data' => $e->getRequestData(),
                'user_friendly_message' => $e->getUserFriendlyMessage()
            ], true);
            
            // Re-throw the exception for the calling code to handle
            throw $e;
        }
    }

    /**
     * Make API request with automatic retry on rate limit errors
     * 
     * @param string $url
     * @param array $params
     * @param string $type
     * @param string $model
     * @param int $maxRetries Maximum number of retries for rate limit errors
     * @param int $retryDelay Delay in seconds between retries
     * @return array
     * @throws RemonlineApiException On API errors
     */
    public function requestWithRetry(
        string $url, 
        array $params, 
        string $type, 
        string $model = '', 
        int $maxRetries = 3, 
        int $retryDelay = 1
    ): array {
        $attempt = 0;
        
        while ($attempt <= $maxRetries) {
            try {
                return $this->request($url, $params, $type, $model);
            } catch (RemonlineApiException $e) {
                if ($e->isRateLimitError() && $attempt < $maxRetries) {
                    $attempt++;
                    $delay = $retryDelay * $attempt; // Exponential backoff
                    
                    $this->pushLogs([
                        'message' => 'Rate limit hit, retrying in ' . $delay . ' seconds',
                        'attempt' => $attempt,
                        'max_retries' => $maxRetries
                    ]);
                    
                    sleep($delay);
                    continue;
                }
                
                // If it's not a rate limit error or we've exhausted retries, re-throw
                throw $e;
            }
        }
        
        // This should never be reached, but just in case
        throw new RemonlineApiException('Maximum retry attempts exceeded');
    }

    /**
     * Универсальный метод для создания сущностей.
     *
     * @param string $url URL для запроса.
     * @param array $data Данные для запроса.
     * @param array $requiredFields Список обязательных полей.
     * @return array Ответ API.
     * @throws \InvalidArgumentException Если обязательные поля отсутствуют.
     */
    public function create(string $url, array $data, array $requiredFields = []): array
    {
        // Проверка обязательных полей
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Поле {$field} является обязательным.");
            }
        }

        // Выполнение POST-запроса
        return $this->request($url, $data, 'POST');
    }
    /**
     * Get data from endpoint with optional pagination
     * 
     * @param string $endpoint API endpoint
     * @param array $arr Additional parameters
     * @param bool $getAllPage Whether to fetch all pages automatically
     * @return array
     */
    public function getData(string $endpoint, array $arr = [], bool $getAllPage = false): array
    {
        $out = [];
        $data = $this->request($endpoint, array_merge($arr), 'GET');

        // Handle different response formats - new API might not have 'data' wrapper
        if (isset($data['data'])) {
            $out['data'] = $data['data'];
            $out['count'] = $data['count'] ?? count($data['data']);
        } else {
            // If no 'data' wrapper, assume the whole response is the data
            $out['data'] = $data;
            $out['count'] = count($data);
        }

        if ($getAllPage && isset($data['count']) && $data['count'] > 50) {
            $countPage = ceil($data['count'] / 50);

            for ($i = 2; $i <= $countPage; $i++) {
                $response = $this->request($endpoint, array_merge($arr, ['page' => $i]), 'GET');
                $responseData = isset($response['data']) ? $response['data'] : $response;
                $out['data'] = array_merge($out['data'], $responseData);
            }
        }

        return $out;
    }

    /**
     * Get API client instance
     * 
     * @return Api
     */
    public function getApiClient(): Api
    {
        return $this->api;
    }


    /**
     * @param mixed $text Message error
     * @param bool $error Boolean error/warning
     * @return void
     */
    public static function pushLogs($text, bool $error = false): void
    {
        // Delegate to Api class for consistent logging
        Api::push_logs($text, $error);
    }
}
