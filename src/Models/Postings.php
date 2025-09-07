<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Postings extends Models
{
    private $endpoint = 'postings';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $posting_id): array
    {
        return $this->api->request($this->endpoint . '/' . $posting_id, [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $posting_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $posting_id, $data, 'PATCH');
    }

    public function delete(int $posting_id): array
    {
        return $this->api->request($this->endpoint . '/' . $posting_id, [], 'DELETE');
    }
}