<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Assets extends Models
{

   
    private $map = [

    ];

    private $endpoint = 'warehouse/assets';
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
    public function getDirectories($parent_id = null)
    {
        return $this->api->request($this->endpoint . '/directories', ['parent_id' => $parent_id], 'GET');

    }

}