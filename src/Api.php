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
        if (!isset($apiKey)) {
            $apiKey = $this->apiKey;
        }

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
                } elseif ($i_data == null) {
                    null;
                } else {
                    $stringPar += "&" . strval($i_data);
                }
            }
            return $stringPar;
        }
        return null;
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
    public function api($url, $par, $type)
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



        $out = json_decode(curl_exec($ch), true);
        //$this->test($out);
        if (curl_errno($ch)) {
            $this->push_logs(curl_error($ch), true);
            throw new Exception('Request failed');
        } else if ($out['success'] === false) {
            $this->push_logs(curl_error($ch), true);
            throw new Exception('Remonline failed: '. $out['message']);
        } else {
            $out['info'] = 'Order';
            return $out;
        }
    }
    private function error()
    {
    }

    private function test($request)
    {
        //$data = json_decode($request, true);
        $data = $request;
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
        $log->pushHandler(new StreamHandler('logs/error.log'));
        if (!$error) {
            $log->warning($text);
        } else {
            $log->error($text);
        }
    }
}
