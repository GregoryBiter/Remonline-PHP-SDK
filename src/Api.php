<?php
namespace Gbit\Remonline;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Api
{
    protected $apiKey;
    protected $httpClient;

    public const APIURL = 'https://api.remonline.app/';

    /**
     * @param string $apiKey API key from RemOnline Settings > API section
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = new Client([
            'base_uri' => self::APIURL,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Make API request to RemOnline
     * 
     * @param string $url API endpoint
     * @param array $params Request parameters
     * @param string $type HTTP method (GET, POST, PATCH)
     * @param string|null $model Optional model name to add to response
     * @return array API response
     * @throws Exception On request failure, invalid API key, or rate limiting
     */
    public function api(string $url, array $params = [], string $type = 'GET', string $model = null): array
    {
        try {
            $options = [];
            if (!empty($params)) {
                if ($type === 'GET') {
                    $options['query'] = $params;
                } else {
                    $options['json'] = $params;
                }
            }

            $response = $this->httpClient->request($type, $url, $options);
            $responseBody = json_decode($response->getBody()->getContents(), true);

            if ($model) {
                $responseBody['model'] = $model;
            }

            return $responseBody;
        } catch (RequestException $e) {
            $this->push_logs('HTTP Error: ' . $e->getMessage(), true);
            throw new Exception('API request failed: ' . $e->getMessage());
        }
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