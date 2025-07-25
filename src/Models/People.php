<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class People extends Models
{

   
    private $map = [
    ];

    private $endpoint = 'contacts/people';
    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = [], bool $getAllPage = false): array
    {
        return $this->response(
            $this->api->request($this->endpoint, $arr, 'GET')
        );
    }

    public function getByID(int $person_id): array
    {
        return $this->response(
            $this->api->request($this->endpoint . '/' . $person_id, [], 'GET')
        );
    }

    /**
     * Получить организацию по ID человека.
     *
     * @param int $person_id Идентификатор человека.
     * @return array Ответ API с информацией об организации.
     */
    public function getOrganization(int $person_id): array
    {
        return $this->api->request($this->endpoint . '/' . $person_id . '/organization', [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->create($this->endpoint, $data, ['first_name']);
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
        if (empty($ids)) {
            throw new \InvalidArgumentException('Массив ids не может быть пустым.');
        }
        return $this->api->request($this->endpoint . '/' . $person_id . '/merge', ['ids' => $ids], 'POST');
    }

}