<?php

namespace Gbit\Roapp\Models;

use Gbit\Roapp\RoappClient;

class Cashbox extends Models
{
    private $endpoint = 'cashbox';

    public function __construct(RoappClient $api)
    {
        parent::__construct($api);
    }
    
    public function get(): array
    {
        return $this->api->request($this->endpoint . '/', [], 'GET');
    }

    public function getTransactions(int $cashbox_id, array $filter_data = []): array
    {
        return $this->api->request($this->endpoint . '/report/' . $cashbox_id, $filter_data, 'GET');
    }
    
    public function getCashflowItems(): array
    {
        return $this->api->request('cashflowitems', [], 'GET');
    }

    public function createPayment(array $data = []): array
    {
        return $this->api->request($this->endpoint . '/payment', $data, 'POST');
    }
}
