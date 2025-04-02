<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

abstract class Models
{
    protected $api;
    private $map = [];

    protected $data = [];

    public function __construct(RemonlineClient $api)
    {
        $this->api = $api;
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

}
