<?php

namespace GBIT\Remonline;

use DateTime;
use Exception;
use Curl\Curl;
use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Api
{
    public Curl $curl;
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
        if (!isset($apiKey)) {
            $apiKey = $this->apiKey;
        }
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setDefaultJsonDecoder($assoc = true);
        $curl->post(self::APIURL . "token/new", [
            "api_key" => $this->apiKey
        ]);
        $our = $curl->response;
        if ($our['success'] == true) {
            $this->tokenInfo = [
                'token' => $our["token"],
                'ts' => time()
            ];
        }
    }
    private function checkToken($url)
    {
        if ($url != 'token/new') {
            if (!isset($this->tokenInfo['token']) || $this->tokenInfo['token'] != NULL || time() - $this->tokenInfo['ts'] >= 580) {
                $this->getToken();
            }
        }
    }
    private function toUrl($par)
    {
        $stringPar = null;
        if (!$par == "" || !$par == null) {
            foreach ($par as $i_key => $i_data) {
                if (is_numeric($i_data)) {
                    $stringPar = $stringPar . "&" . $i_key . "=" . strval($i_data);
                } else if (is_array($i_data)) {
                    foreach ($i_data as $v_key => $v) {
                        $stringPar = $stringPar . "&" . $i_key . "=" . strval($v);
                    }
                } elseif($i_data == null){
                    null;
                }else {
                    $stringPar += "&" . strval($i_data);
                }
            }
            return $stringPar;
        }
        return null;
    }
    public function api($url, $par, $type)
    {
        $curl = new Curl();
        self::push_logs("URL: " . json_encode($url) . "PAR: " . json_encode($par) . "Type: " . json_encode($type));
        $this->checkToken($url);
        if ($type == "GET") {

            $curl->setDefaultJsonDecoder($assoc = true);
            $curl->get(self::APIURL . $url . '?' . http_build_query(["token" => $this->tokenInfo['token']]) . $this->toUrl($par));
        } else if ($type == "POST") {
            $curl->setDefaultJsonDecoder($assoc = true);
            $curl->setHeader('Content-Type', 'application/json');
            $curl->post(self::APIURL . $url, [
                "token" => $this->tokenInfo['token'],
                $par
            ]);
        }
        echo "<pre>";
        if ($curl->error) {
            // echo 'Error: ' . $curl->errorMessage . "\n";
            // echo 'Response:' . var_dump($curl->response) . "\n";
            return $curl->response;
        } else {

            $out = $curl->response;
            $out['info'] = 'Order';
            return $out;
            // echo 'Response:' . "\n";
            // var_dump($curl->response);
        }
        // var_dump($curl->requestHeaders);
        // var_dump($curl->responseHeaders);
    }

    private function test($request)
    {
        $data = json_decode($request, true);
        if ($data['success'] == true || !$data['success'] == null) {
            return $data;
        } else {
            throw new Exception('Error: ' .  var_dump($data));
            return var_dump($data);
        }
    }
    public static function push_logs($text, $error = false)
    {

        $log = new Logger('debag');
        $log->pushHandler(new StreamHandler('logs/viber-api.log'));
        if (!$error) {
            $log->warning($text);
        } else {
            $log->error($text);
        }
    }
}
