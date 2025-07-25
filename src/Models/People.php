<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class People extends Models
{

   
    private $map = [
        'sort_dir' => '',
        'types' => '',
        'branches' => '',
        'brands' => '',
        'ids' => '',
        'id_labels[]' => '',
        'statuses' => '',
        'managers' => '',
        'engineers' => '',
        'clients_ids' => '',
        'client_names' => '',
        'client_phones' => '',
        'created_at' => '',
        'done_at' => '',
        'modified_at' => '',
        'closed_at' => ''
    ];

    private $endpoint = 'contacts/people';
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
    public function getOrganization($person_id)
    {
        return $this->api->request($this->endpoint . '/' . $person_id . '/organization', [], 'GET');

    }

    /*
    Body Params
first_name
string
required
Person first name

last_name
string
Person last name

email
string
Valid email

phones
array of objects
List of phone numbers


ADD object
notes
string
Notes text

address
string
Person address

supplier
boolean
Defaults to false
Is this person your supplier?


manager_id
int32
Employee ID

ad_campaign_id
int32
Ad Campaign ID

discount_code
string
Discount code

custom_fields
json
Custom fields values in format {"f123": "value", "f234": "value"}, where "f123" and "f234" is a custom field id.
    */
    public function create(array $data = []): array
    {
        // Используем универсальный метод из $this->api
        return $this->api->create($this->endpoint, $data, ['first_name']);
    }

    /*
    Path Params
    person_id
    int32
    required
    Person ID

    Body Params
    first_name
    string
    Person first name

    last_name
    string
    Person last name

    email
    string
    Valid email

    phones
    array of objects
    List of phone numbers


    ADD object
    notes
    string
    Notes text

    address
    string
    Person address

    supplier
    boolean
    Defaults to false
    Is this person your supplier?


    manager_id
    int32
    Employee ID

    ad_campaign_id
    int32
    Ad Campaign ID

    discount_code
    string
    Discount code

    custom_fields
    json
    Custom fields values in format {"f123": "value", "f234": "value"}, where "f123" and "f234" is a custom field id.
    */

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
        // Проверка, что массив $ids не пуст
        if (empty($ids)) {
            throw new \InvalidArgumentException('Массив ids не может быть пустым.');
        }

        // Выполнение запроса
        return $this->api->request($this->endpoint . '/' . $person_id . '/merge', ['ids' => $ids], 'POST');
    }

}