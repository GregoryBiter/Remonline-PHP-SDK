<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Assets extends Models
{
    private $endpoint = 'warehouse/assets';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $asset_id): array
    {
        return $this->api->request($this->endpoint . '/' . $asset_id, [], 'GET');
    }

    public function getDirectories($parent_id = null): array
    {
        return $this->api->request($this->endpoint . '/directories', ['parent_id' => $parent_id], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $asset_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $asset_id, $data, 'PATCH');
    }

    public function delete(int $asset_id): array
    {
        return $this->api->request($this->endpoint . '/' . $asset_id, [], 'DELETE');
    }
}