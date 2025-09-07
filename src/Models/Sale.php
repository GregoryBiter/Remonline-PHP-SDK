<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Sale extends Models
{
    private $endpoint = 'retail/sales';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $sale_id): array
    {
        return $this->api->request($this->endpoint . '/' . $sale_id, [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $sale_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $sale_id, $data, 'PATCH');
    }

    public function delete(int $sale_id): array
    {
        return $this->api->request($this->endpoint . '/' . $sale_id, [], 'DELETE');
    }

    public function getItems(int $sale_id): array
    {
        return $this->api->request($this->endpoint . '/' . $sale_id . '/items', [], 'GET');
    }

    public function addItem(int $sale_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $sale_id . '/items', $data, 'POST');
    }

    public function updateItem(int $sale_id, int $item_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $sale_id . '/items/' . $item_id, $data, 'PATCH');
    }

    public function deleteItem(int $sale_id, int $item_id): array
    {
        return $this->api->request($this->endpoint . '/' . $sale_id . '/items/' . $item_id, [], 'DELETE');
    }

    public function complete(int $sale_id): array
    {
        return $this->api->request($this->endpoint . '/' . $sale_id . '/complete', [], 'POST');
    }

    public function refund(int $sale_id, array $data = []): array
    {
        return $this->api->request($this->endpoint . '/' . $sale_id . '/refund', $data, 'POST');
    }
}
