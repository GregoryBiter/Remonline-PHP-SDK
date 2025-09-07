<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Kit extends Models
{
    private $endpoint = 'kits';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $kit_id): array
    {
        return $this->api->request($this->endpoint . '/' . $kit_id, [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $kit_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $kit_id, $data, 'PATCH');
    }

    public function delete(int $kit_id): array
    {
        return $this->api->request($this->endpoint . '/' . $kit_id, [], 'DELETE');
    }

    public function getItems(int $kit_id): array
    {
        return $this->api->request($this->endpoint . '/' . $kit_id . '/items', [], 'GET');
    }

    public function addItem(int $kit_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $kit_id . '/items', $data, 'POST');
    }

    public function updateItem(int $kit_id, int $item_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $kit_id . '/items/' . $item_id, $data, 'PATCH');
    }

    public function deleteItem(int $kit_id, int $item_id): array
    {
        return $this->api->request($this->endpoint . '/' . $kit_id . '/items/' . $item_id, [], 'DELETE');
    }

    public function search(string $query): array
    {
        return $this->api->request($this->endpoint, ['search' => $query], 'GET');
    }
}
