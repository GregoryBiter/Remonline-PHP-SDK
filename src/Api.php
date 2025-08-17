<?php
namespace Gbit\Remonline;
use \Exception;

class Api
{
    protected $apiKey;

    public const APIURL = 'https://api.remonline.app/';

    /**
     * @param string $apiKey API key from RemOnline Settings > API section
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Make API request to RemOnline
     * 
     * @param string $url API endpoint
     * @param array $params Request parameters
     * @param string $type HTTP method (GET, POST, PATCH, DELETE)
     * @param string|null $model Optional model name to add to response
     * @return array API response
     * @throws RemonlineApiException On request failure, invalid API key, rate limiting, or JSON parsing errors
     */
    public function api(string $url, array $params = [], string $type = 'GET', string $model = null): array
    {
        $fullUrl = self::APIURL . ltrim($url, '/');
        
        // Initialize cURL
        $ch = curl_init();        // Prepare headers
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Accept: application/json',
        ];
        
        // Add Content-Type only if we have data to send as JSON
        $hasJsonData = !empty($params) && in_array(strtoupper($type), ['POST', 'PATCH', 'DELETE']);
        if ($hasJsonData) {
            $headers[] = 'Content-Type: application/json';
        }
        
        // Set basic cURL options
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        
        // Handle different HTTP methods
        switch (strtoupper($type)) {
            case 'GET':
                if (!empty($params)) {
                    $fullUrl .= '?' . http_build_query($params);
                }
                curl_setopt($ch, CURLOPT_URL, $fullUrl);
                break;
                
            case 'POST':
                curl_setopt($ch, CURLOPT_URL, $fullUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                if (!empty($params)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                }
                break;
                
            case 'PATCH':
                curl_setopt($ch, CURLOPT_URL, $fullUrl);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                if (!empty($params)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                }
                break;
                
            case 'DELETE':
                curl_setopt($ch, CURLOPT_URL, $fullUrl);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($params)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                }
                break;
                
            default:
                curl_close($ch);
                throw new Exception('Unsupported HTTP method: ' . $type);
        }
        
        // Execute request
        $response = curl_exec($ch);
        
        // Check for cURL errors
        if ($response === false) {
            $curlError = curl_error($ch);
            $curlErrno = curl_errno($ch);
            curl_close($ch);
            
            $logData = [
                'curl_error' => $curlError,
                'curl_errno' => $curlErrno,
                'url' => $fullUrl,
                'method' => $type,
                'params' => $params
            ];
            
            $this->push_logs('cURL Error: ' . json_encode($logData, JSON_UNESCAPED_UNICODE), true);
            
            throw new RemonlineApiException(
                'API request failed: ' . $curlError,
                0,
                ['curl_error' => $curlError, 'curl_errno' => $curlErrno],
                $fullUrl,
                $params
            );
        }
        
        // Get HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Parse JSON response first to get error details
        $responseBody = json_decode($response, true);
        
        // Check for HTTP errors
        if ($httpCode >= 400) {
            $errorMessage = 'API request failed with HTTP status: ' . $httpCode;
            $errorDetails = [];
            
            // Try to extract error details from JSON response
            if (json_last_error() === JSON_ERROR_NONE && is_array($responseBody)) {
                // Common error fields in APIs
                $errorFields = ['error', 'message', 'error_description', 'errors', 'detail', 'details'];
                
                foreach ($errorFields as $field) {
                    if (isset($responseBody[$field])) {
                        $errorDetails[$field] = $responseBody[$field];
                    }
                }
                
                // If we found error details, include them in the exception
                if (!empty($errorDetails)) {
                    $errorMessage .= '. Error details: ' . json_encode($errorDetails, JSON_UNESCAPED_UNICODE);
                }
            }
            
            // Log the full error information
            $logData = [
                'http_code' => $httpCode,
                'url' => $fullUrl,
                'method' => $type,
                'request_params' => $params,
                'response' => $response,
                'error_details' => $errorDetails
            ];
            
            $this->push_logs('HTTP Error: ' . json_encode($logData, JSON_UNESCAPED_UNICODE), true);
            
            // Create custom exception with error details
            throw new RemonlineApiException(
                $errorMessage,
                $httpCode,
                $errorDetails,
                $fullUrl,
                $params
            );
        }
        
        // Check for JSON parsing errors only for successful responses
        if (json_last_error() !== JSON_ERROR_NONE) {
            $jsonError = 'Invalid JSON response: ' . json_last_error_msg();
            $logData = [
                'json_error' => json_last_error_msg(),
                'json_error_code' => json_last_error(),
                'response' => $response,
                'url' => $fullUrl,
                'method' => $type
            ];
            
            $this->push_logs('JSON Error: ' . json_encode($logData, JSON_UNESCAPED_UNICODE), true);
            
            throw new RemonlineApiException(
                $jsonError,
                0,
                ['json_error' => json_last_error_msg(), 'json_error_code' => json_last_error()],
                $fullUrl,
                $params
            );
        }
        
        if ($model) {
            $responseBody['model'] = $model;
        }
        
        return $responseBody;
    }

    /**
     * @param mixed $text Message error
     * @param bool $error Boolean error/warning
     * @return void
     */
    /**
     * Записывает лог в файл logs/error.log вручную.
     *
     * @param mixed $text Сообщение или данные для лога
     * @param bool $error true - ошибка, false - предупреждение
     * @return void
     */
    public static function push_logs($text, bool $error = false): void
    {
        $logDir = __DIR__ . '/../logs';
        $logFile = $logDir . '/error.log';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $date = date('Y-m-d H:i:s');
        $type = $error ? 'ERROR' : 'WARNING';
        $message = is_string($text) ? $text : json_encode($text, JSON_UNESCAPED_UNICODE);
        $logLine = "[$date] [$type] $message\n";
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
}