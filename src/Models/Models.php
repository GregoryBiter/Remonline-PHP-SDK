<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\Api;

class Models
{
    public $count;
    private $page;
    public function __construct(Api &$api)
    {
        $this->user = $api;
    }
    public function page($page)
    {
        $this->page = $page;
        return $this;
    }


    public function getAll()
    {
        $data = $this->user->api('order/', array_merge($arr), 'GET');
        $countPage = $data['count'] / 50;
        if ($data['count'] % 50 > 0) {
            $countPage++;
        }

        for ($i = 1; $i <= $countPage; $i++) {
            $response = $this->user->api('order/', array_merge($arr), 'GET');
            array_merge($data['data'], $response['data']);
        }
        $data['page'] = 'All page';
        return $data;
}
}