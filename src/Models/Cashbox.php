<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Cashbox extends Models
{
    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }
    
    public function getCashbox(): array
    {
        return $this->api->getData('cashbox/', []);
    }

    public function getCashboxTransactions(int $cashbox_id, array $filter_data = [], bool $getAllPage)  : array
    {
        $endpoint = 'cashbox/report/' . $cashbox_id;
        return $this->api->getData($endpoint,  $filter_data, $getAllPage);
    }
    
    public function getCashflowItems(): array
    {
        return $this->api->getData('cashflowitems/', []);
    }

    public function createPayment($data = []): array
    {
        return $this->api->request('cashbox/payment/', $data, 'POST');
    }
}
