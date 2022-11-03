<?php

namespace GBIT\Remonline;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Http
{

    public static function send($url, $data = null, $type = "GET", $header, $json)
    {
        $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => $url,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 30,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "POST",
        //     CURLOPT_POSTFIELDS => json_encode($data), // отправка кода
        //     CURLOPT_HTTPHEADER => $header,
        // ));
        curl_setopt($curl, CURLOPT_URL, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, true);

        if ($type == "POST") {
            if ($json) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            } else {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);



        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err != null) {
            $status = false;
            $message = "it is error";
            self::push_logs(json_encode($response));
            if ($err != null) {
                self::push_logs(json_encode($err));
            }
        };

        return [
            'message' => $message,
            'data' => $response,
            'error' => $err,
            'status' => $status,
        ];
    }
    public static function post($url, $data, $header = '', $json = false)
    {
        return self::send($url, $data, $type = "POST", $header, $json);
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
