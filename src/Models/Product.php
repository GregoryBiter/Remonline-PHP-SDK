<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Product extends Models
{
    private $endpoint = 'products';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $product_id): array
    {
        return $this->api->request($this->endpoint . '/' . $product_id, [], 'GET');
    }

    public function getCategories(): array
    {
        return $this->api->request('warehouse/categories', [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $product_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $product_id, $data, 'PATCH');
    }

    public function delete(int $product_id): array
    {
        return $this->api->request($this->endpoint . '/' . $product_id, [], 'DELETE');
    }
}
