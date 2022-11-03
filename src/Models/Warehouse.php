<?php

namespace GBIT\Remonline\Models;

use GBIT\Remonline\User;

class Warehouse
{

    private $user;
    public $count;
    private $page;
    public function __construct(User &$user)
    {
        $this->user = $user;
    }
    public function get($arr = [])
    {
        return $this->user->api('order/', array_merge($arr, [
            'page' => $this->page
        ]), 'GET');
    }
    public function page($page)
    {
        $this->page = $page;
        return $this;
    }
    public function getAllPage($arr = [])
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
    public function getCustomFields()
    {
        $response = $this->user->api('order/custom-fields/', [], 'GET');
        return $response;
    }
}
