<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\Api;

class Models
{
    protected $api;
    protected $map = [];

    protected $data = [];

    public function __construct(string $api)
    {
        $this->api = new Api($api);
    }

    // public function __set($name, $value)
    // {
    //     if (isset($this->map[$name])) {
    //         $this->data[$this->map[$name]] = $value;
    //     }
    // }

    // public function __get($name)
    // {
    //     if (isset($this->map[$name])) {
    //         return $this->data[$this->map[$name]] ?? null;
    //     }
    //     return null;
    // }
    protected function getAllMapFields()
    {
        $mapArray = [];

        foreach ($this->map as $key => $value) {
            if (isset($this->{$value}) && $this->{$value} != null){

            }
            $mapArray[$key] = $this->{$value};
        }

        return $mapArray;
    }
// TODO сделать фильтр работающий с массивом вида ['sort_dir' => 'asc', 'types' => 'electronics']
    public function filter(array $filter)
    {
        foreach ($filter as $key => $value) {
            if (isset($this->map[$key])) {
                $this->data[$this->map[$key]] = $value;
            }
        }
    }
    protected function getData($endpoint, $arr = [], $getAllPage = false)
    {
        $out = [];
        $data = $this->api->api($endpoint, array_merge($arr), 'GET');
        $out['data'] = $data['data'];
        if ($getAllPage) {
            $countPage = $data['count'] / 50;
            if ($data['count'] % 50 > 0) {
                $countPage++;
            }
            
            for ($i = 1; $i <= $countPage; $i++) {
                $response = $this->api->api($endpoint, array_merge($arr, ['page' => $i]), 'GET');
                $out['data'] = array_merge($out['data'], $response['data']);
            }
        }
        $out['count'] = $data['count'];
        return $out;
    }
}
