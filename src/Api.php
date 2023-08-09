<?php

namespace Gbit\Remonline;

use DateTime;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Api
{
    public $apiKey;
    protected $tokenInfo = [];
    public const APIURL = 'https://api.remonline.app/';
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->tokenInfo['token'] = NULL;
    }
    /**
     * @param string $apiKey key api Remonlie
     */
    public function getToken($apiKey = null): void
    {
        if ($apiKey === null) {
            $apiKey = $this->apiKey;
        }

        $url = self::APIURL . 'token/new';
        $data = [
            'api_key' => $apiKey,
        ];
        $headers = [
            'Content-Type: application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = json_decode(curl_exec($ch), true);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            throw new Exception('Failed to make API request: ' . $error);
        }

        if ($httpCode !== 200 || !$response['success']) {
            throw new Exception('Failed to get token: ' . $response['message']);
        }

        $this->tokenInfo = [
            'token' => $response['token'],
            'ts' => time(),
        ];
    }
    /**
     * @param string $url pass url
     */
    private function checkToken($url): void
    {
        if ($url != 'token/new')
            if (!isset($this->tokenInfo['token']) || $this->tokenInfo['token'] != NULL || time() - $this->tokenInfo['ts'] >= 580)
                $this->getToken();
    }
    /**
     * @param array $params the query parameters
     * @return string url query to Remonline
     */
    
    private function toUrl($params)
    {
        $urlParams = '';

        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                if (is_numeric($value)) {
                    $urlParams .= '&' . $key . '=' . strval($value);
                } elseif (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $urlParams .= '&' . $key . '=' . strval($subValue);
                    }
                }
            }
        }

        return $urlParams;
    }

    /**
     * @param string $url
     * @param array $params
     * @param string $type
     * @param string|null $model
     * @return array
     */
    public function api(string $url, array $params, string $type, string $model = null): array
    {
        $this->checkToken($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            'Content-Type: application/json'
        ];

        if ($type === "GET") {
            $queryString = http_build_query(["token" => $this->tokenInfo['token']]) . $this->toUrl($params);
            curl_setopt($ch, CURLOPT_URL, self::APIURL . $url . '?' . $queryString);
        } else if ($type === "POST") {
            $requestData = ["token" => $this->tokenInfo['token']] + $params;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, self::APIURL . $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        }
        $request = json_decode(curl_exec($ch), true);
        if (curl_errno($ch)) {
            $this->push_logs(curl_error($ch), true);
            throw new Exception('Request failed');
        } else if ($request['success'] === false) {
            $this->push_logs($request, true);
            throw new Exception('Remonline failed: ' . json_encode($request));
        } else {
            if ($model) {
                $request['model'] = $model;
            }

            return $request;
        }
    }
    /**
     * @param $text Message error
     * @param $error Boolean error/warning
     * @return none
     * @throws
     **/
    public static function push_logs($text, $error = false): void
    {

        $log = new Logger('debag');
        $log->pushHandler(new StreamHandler('logs/error.log'));
        if (!$error) {
            $log->warning(json_encode($text));
        } else {
            $log->error(json_encode($text));
        }
    }
}
