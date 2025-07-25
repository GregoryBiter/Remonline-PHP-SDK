<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Organization extends Models
{
    private $map = [

    ];
    private $endpoint = 'contacts/organizations';
    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }


    /*Query Params
    page
    int32
    Defaults to 1
    Page number

    names
    array of strings
    List of names


    ADD string
    phones
    array of strings
    List of phones


    ADD string
    managers
    array of int32s
    List of Employee IDs


    ADD int32
    */
    public function get(array $arr = [], bool $getAllPage = false): array
    {
        // return $this->api->getData($this->endpoint, $arr, $getAllPage);
        return $this->response(
            $this->api->request($this->endpoint, $arr, 'GET')
        );
    }

    public function getByID($person_id)
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
    public function getPeople($organization_id)
    {
        return $this->api->request($this->endpoint . '/' . $organization_id . '/people', [], 'GET');

    }
    public function addPeople(int $organization_id, array $contact_id): array
    {
        // Используем универсальный метод из $this->api
        return $this->api->request($this->endpoint . '/' . $organization_id . '/people', ['contact_id' => $contact_id], 'POST');
    }

    public function deletePeople(int $organization_id, int $contact_id): array
    {
        // Используем универсальный метод из $this->api
        return $this->api->request($this->endpoint . '/' . $organization_id . '/people/' . $contact_id, [], 'DELETE');
    }

    public function create(array $data = []): array
    {
        // Используем универсальный метод из $this->api
        return $this->api->create($this->endpoint, $data, ['first_name']);
    }

    public function update(int $organization_id, array $data): array
    {
        return $this->api->request($this->endpoint . "/" . $organization_id, $data, 'PATCH');
    }

    public function delete(int $organization_id): array
    {
        return $this->api->request($this->endpoint . '/' . $person_id, [], 'DELETE');
    }
    public function addComment(int $organization_id, string $comment): array
    {
        return $this->api->request($this->endpoint . '/' . $organization_id . '/comments', ['comment' => $comment], 'POST');
    }

    public function merge(int $organization_id, array $ids): array
    {
        // Проверка, что массив $ids не пуст
        if (empty($ids)) {
            throw new \InvalidArgumentException('Массив ids не может быть пустым.');
        }

        // Выполнение запроса
        return $this->api->request($this->endpoint . '/' . $organization_id . '/merge', ['ids' => $ids], 'POST');
    }

}