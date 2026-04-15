<?php

namespace Gbit\Roapp;

use Exception;

/**
 * RoappClient - High-level client for Roapp API
 */
class RoappClient
{
    const POST = 'POST';
    const GET = 'GET';
    const PUT = 'PUT';

    protected $api;

    public function __construct(string $apiKey)
    {
        $this->api = new Api($apiKey);
    }

    public function request(string $url, array $params, string $type, string $model = ''): array
    {
        try {
            return $this->api->api($url, $params, $type, $model);
        } catch (RoappApiException $e) {
            $this->pushLogs([
                'error_type' => 'RoappApiException',
                'message' => $e->getMessage(),
                'http_code' => $e->getHttpCode(),
                'error_details' => $e->getErrorDetails(),
                'api_url' => $e->getApiUrl(),
                'request_data' => $e->getRequestData(),
                'user_friendly_message' => $e->getUserFriendlyMessage()
            ], true);

            throw $e;
        }
    }

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
            } catch (RoappApiException $e) {
                if ($e->isRateLimitError() && $attempt < $maxRetries) {
                    $attempt++;
                    $delay = $retryDelay * $attempt;
                    $this->pushLogs([
                        'message' => 'Rate limit hit, retrying in ' . $delay . ' seconds',
                        'attempt' => $attempt,
                        'max_retries' => $maxRetries
                    ]);
                    sleep($delay);
                    continue;
                }

                throw $e;
            }
        }

        throw new RoappApiException('Maximum retry attempts exceeded');
    }

    public function create(string $url, array $data, array $requiredFields = []): array
    {
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Поле {$field} является обязательным.");
            }
        }

        return $this->request($url, $data, 'POST');
    }

    public function getData(string $endpoint, array $arr = [], bool $getAllPage = false): array
    {
        $out = [];
        $data = $this->request($endpoint, array_merge($arr), 'GET');

        if (isset($data['data'])) {
            $out['data'] = $data['data'];
            $out['count'] = $data['count'] ?? count($data['data']);
        } else {
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

    public function getApiClient(): Api
    {
        return $this->api;
    }

    public static function pushLogs($text, bool $error = false): void
    {
        Api::push_logs($text, $error);
    }
}
