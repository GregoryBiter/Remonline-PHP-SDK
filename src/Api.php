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
    public function getToken($apiKey = null)
    {
        if (!isset($apiKey))
            $apiKey = $this->apiKey;
        $ch = curl_init(self::APIURL . "token/new");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["api_key" => $apiKey]));
        $our = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if ($our['success'] == true) {
            $this->tokenInfo = [
                'token' => $our["token"],
                'ts' => time()
            ];
        }
    }
    private function checkToken($url)
    {
        if ($url != 'token/new')
            if (!isset($this->tokenInfo['token']) || $this->tokenInfo['token'] != NULL || time() - $this->tokenInfo['ts'] >= 580)
                $this->getToken();
    }
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
     * Send request to Remonline
     *
     * Undocumented function long description
     *
     * @param String $url url from Remonline
     * @param Array $par data to requst 
     * @param String $type "GET"/"POST"
     * @return Array json out
     **/
    public function api($url, $par, $type, $list = null)
    {
        $this->checkToken($url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            'Content-Type: application/json'
        ];
        if ($type == "GET") {
            curl_setopt($ch, CURLOPT_URL, self::APIURL . $url . '?' . http_build_query(["token" => $this->tokenInfo['token']]) . $this->toUrl($par));
            //echo $this->toUrl($par);
        } else if ($type == "POST") {
            $headers = [
                'Content-Type: application/json'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, self::APIURL . $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["token" => $this->tokenInfo['token']] + $par));
        }
        $request = json_decode(curl_exec($ch), true);
        if (curl_errno($ch)) {
            $this->push_logs(curl_error($ch), true);
            throw new Exception('Request failed');
        } else if ($request['success'] === false) {
            $this->push_logs($request, true);
            throw new Exception('Remonline failed: ' . $request['message']);
        } else {
            if ($list) {
                $request['list'] = 'Order';
            }

            return $request;
        }
    }
    public static function push_logs($text, $error = false)
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
