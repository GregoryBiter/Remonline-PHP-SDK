<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\Api;

class Cashbox extends Models
{
    // private $user;
    protected $map = [

    ];
    public function __construct(string $api)
    {
        parent::__construct($api);
    }
    public function get($arr = [], $getAllPage = false)
    {
        return $this->getData('cashbox/', $arr, $getAllPage);
    }
    public function getReport($cashbox_id, $arr = [])
    {
        return $this->getData('cashbox/report/'.$cashbox_id, $arr, false);
    }

}
