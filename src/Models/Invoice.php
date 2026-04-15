<?php

namespace Gbit\Roapp\Models;

use Gbit\Roapp\RoappClient;

class Invoice extends Models
{
    private $endpoint = 'invoices';

    public function __construct(RoappClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $invoice_id): array
    {
        return $this->api->request($this->endpoint . '/' . $invoice_id, [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $invoice_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $invoice_id, $data, 'PATCH');
    }

    public function delete(int $invoice_id): array
    {
        return $this->api->request($this->endpoint . '/' . $invoice_id, [], 'DELETE');
    }
}
