<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

abstract class Models
{
    protected $api;
    private $map = [];

    protected $data = [];
    protected $meta = [];

    protected $page = null;

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

    protected function response($response): array
    {
        $this->page = null;
        if (isset($response['count'])) {
            $this->meta['count'] = $response['count'];
        }
        if (isset($response['page'])) {
            $this->meta['page'] = $response['page'];
        }
        if (isset($response['page_size'])) {
            $this->meta['page_size'] = $response['page_size'];
        }
        if (isset($response['total_pages'])) {
            $this->meta['total_pages'] = $response['total_pages'];
        }
        if (isset($response['data'])) {
           
            $this->data = $response['data'];
            return $response['data'];
        }
        return $response;
    }

    /*
    [count] => 13266
    [page] => 1
    [page_size] => 50
    [total_pages] => 266*/
    public function meta($key = null)
    {
        if ($key) {
            return $this->meta[$key];
        }
        return $this->meta;
    }

    public function page($page)
    {
        $this->page = $page;
        return $this;
    }

}
