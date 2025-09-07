<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class People extends Models
{
    private $endpoint = 'contacts/people';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $person_id): array
    {
        return $this->api->request($this->endpoint . '/' . $person_id, [], 'GET');
    }

    public function getOrganization(int $person_id): array
    {
        return $this->api->request($this->endpoint . '/' . $person_id . '/organization', [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $person_id, array $data): array
    {
        return $this->api->request($this->endpoint . "/" . $person_id, $data, 'PATCH');
    }

    public function delete(int $person_id): array
    {
        return $this->api->request($this->endpoint . '/' . $person_id, [], 'DELETE');
    }

    public function addComment(int $person_id, string $comment): array
    {
        return $this->api->request($this->endpoint . '/' . $person_id . '/comments', ['comment' => $comment], 'POST');
    }

    public function merge(int $person_id, array $ids): array
    {
        return $this->api->request($this->endpoint . '/' . $person_id . '/merge', ['ids' => $ids], 'POST');
    }
}