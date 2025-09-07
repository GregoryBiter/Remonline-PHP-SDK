<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Service extends Models
{
    private $endpoint = 'services';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $service_id): array
    {
        return $this->api->request($this->endpoint . '/' . $service_id, [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $service_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $service_id, $data, 'PATCH');
    }

    public function delete(int $service_id): array
    {
        return $this->api->request($this->endpoint . '/' . $service_id, [], 'DELETE');
    }

    public function getCategories(): array
    {
        return $this->api->request($this->endpoint . '/categories', [], 'GET');
    }

    public function search(string $query): array
    {
        return $this->api->request($this->endpoint, ['search' => $query], 'GET');
    }

    public function getPrices(int $service_id): array
    {
        return $this->api->request($this->endpoint . '/' . $service_id . '/prices', [], 'GET');
    }
}
