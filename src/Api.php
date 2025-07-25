<?php
namespace Gbit\Remonline;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
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
     * @throws Exception On request failure, invalid API key, or rate limiting
     */
    public function api(string $url, array $params = [], string $type = 'GET', string $model = null): array
    {
        $fullUrl = self::APIURL . ltrim($url, '/');
        
        // Initialize cURL
        $ch = curl_init();
        
        // Prepare headers
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
            $error = curl_error($ch);
            curl_close($ch);
            $this->push_logs('cURL Error: ' . $error, true);
            throw new Exception('API request failed: ' . $error);
        }
        
        // Get HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Check for HTTP errors
        if ($httpCode >= 400) {
            $this->push_logs('HTTP Error: Status code ' . $httpCode . ', Response: ' . $response, true);
            throw new Exception('API request failed with HTTP status: ' . $httpCode);
        }
        
        // Parse JSON response
        $responseBody = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->push_logs('JSON Error: ' . json_last_error_msg() . ', Response: ' . $response, true);
            throw new Exception('Invalid JSON response: ' . json_last_error_msg());
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
    public static function push_logs($text, bool $error = false): void
    {
        $log = new Logger('debug');
        $log->pushHandler(new StreamHandler('logs/error.log'));
        if (!$error) {
            $log->warning(json_encode($text));
        } else {
            $log->error(json_encode($text));
        }
    }
}