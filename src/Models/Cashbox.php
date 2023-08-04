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
    public function get($getAllPage = false, array $array = null)
    {
        if ($array == null) {
            $array = $this->getAllMapFields();
        }
        return $this->getData('cashbox/', $array, $getAllPage);
    }
    public function getReport($cashbox_id, $getAllPage = false, $array = null)
    {
        if ($array == null) {
            $array = $this->getAllMapFields();
        }
        return $this->getData('cashbox/report/'.$cashbox_id, $array, $getAllPage);
    }

}
