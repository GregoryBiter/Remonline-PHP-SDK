<?php

namespace Gbit\Remonline;

use DateTime;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class RemonlineClient
{
    const POST = 'POST';
    const GET = 'GET';
    const PUT = 'PUT';
    protected $apiKey;
    protected $tokenInfo = [];
    protected const APIURL = 'https://api.remonline.app/';
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
        if ($url !== 'token/new') {
            if (!isset($this->tokenInfo['token'])
                || $this->tokenInfo['token'] === null
                || time() - $this->tokenInfo['ts'] >= 580) {
                $this->getToken();
            }
        }
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
    public function request(string $url, array $params, string $type, string $model = ''): array
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
        if (curl_error($ch)) {
            $this->pushLogs(curl_error($ch), true);
            throw new Exception('Request failed');
        } else if (!isset($request['success']) || $request['success'] === false) {
            $this->pushLogs($request, true);
            throw new Exception('Remonline failed: ' . json_encode($request));
        } else {
            if ($model) {
                $request['model'] = $model;
            }

            return $request;
        }
    }



    public function getData($endpoint, $arr = [], $getAllPage = false)
    {
        $out = [];
        $data = $this->request($endpoint, array_merge($arr), 'GET');
        $out['data'] = $data['data'];
        if ($getAllPage) {
            $countPage = $data['count'] / 50;
            if ($data['count'] % 50 > 0) {
                $countPage++;
            }
            
            for ($i = 1; $i <= $countPage; $i++) {
                $response = $this->request($endpoint, array_merge($arr, ['page' => $i]), 'GET');
                $out['data'] = array_merge($out['data'], $response['data']);
            }
        }
        $out['count'] = $data['count'];
        return $out;
    }


    /**
     * @param $text Message error
     * @param $error Boolean error/warning
     * @return none
     * @throws
     **/
    public static function pushLogs($text, $error = false): void
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
