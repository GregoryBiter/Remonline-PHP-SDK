<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Organization extends Models
{
    private $endpoint = 'contacts/organizations';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $organization_id): array
    {
        return $this->api->request($this->endpoint . '/' . $organization_id, [], 'GET');
    }

    public function getPeople(int $organization_id): array
    {
        return $this->api->request($this->endpoint . '/' . $organization_id . '/people', [], 'GET');
    }

    public function addPeople(int $organization_id, array $contact_id): array
    {
        return $this->api->request($this->endpoint . '/' . $organization_id . '/people', ['contact_id' => $contact_id], 'POST');
    }

    public function deletePeople(int $organization_id, int $contact_id): array
    {
        return $this->api->request($this->endpoint . '/' . $organization_id . '/people/' . $contact_id, [], 'DELETE');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $organization_id, array $data): array
    {
        return $this->api->request($this->endpoint . "/" . $organization_id, $data, 'PATCH');
    }

    public function delete(int $organization_id): array
    {
        return $this->api->request($this->endpoint . '/' . $organization_id, [], 'DELETE');
    }

    public function addComment(int $organization_id, string $comment): array
    {
        return $this->api->request($this->endpoint . '/' . $organization_id . '/comments', ['comment' => $comment], 'POST');
    }

    public function merge(int $organization_id, array $ids): array
    {
        return $this->api->request($this->endpoint . '/' . $organization_id . '/merge', ['ids' => $ids], 'POST');
    }
}