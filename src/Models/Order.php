<?php

namespace GBIT\Remonline\Models;

use GBIT\Remonline\Api;

class Order
{

    public $sort_dir;
    public $types;
    public $branches;
    public $brands;
    public $ids;
    public $id_labels = [];
    public $statuses;
    public $managers;
    public $engineers;
    public $clients_ids;
    public $client_names;
    public $client_phones;
    public $created_at;
    public $done_at;
    public $modified_at;
    public $closed_at;
    private $user;
    public $count;
    private $page;
    public function __construct(Api &$api)
    {
        $this->user = $api;
    }
    public function get($arr = [])
    {
        return $this->user->api('order/', array_merge($arr, [
            'page' => $this->page,
            'sort_dir' => $this->sort_dir,
            'types' => $this->types,
            'branches' => $this->branches,
            'brands' => $this->brands,
            'ids' => $this->ids,
            'id_labels[]' => $this->id_labels,
            'statuses' => $this->statuses,
            'managers' => $this->managers,
            'engineers' => $this->engineers,
            'clients_ids' => $this->clients_ids,
            'client_names' => $this->client_names,
            'client_phones' => $this->client_phones,
            'created_at' => $this->created_at,
            'done_at' => $this->done_at,
            'modified_at' => $this->modified_at,
            'closed_at' => $this->closed_at
        ]), 'GET');
    }
    public function page($page)
    {
        $this->page = $page;
        return $this;
    }
    public function getAllPage($arr = [])
    {
        $data = $this->user->api('order/',  array_merge($arr), 'GET');
        $countPage = $data['count'] / 50;
        if ($data['count'] % 50 > 0) {
            $countPage++;
        }
        $out['data'] = $data['data'];
        for ($i = 1; $i <= $countPage; $i++) {
            $response = $this->user->api('order/', array_merge($arr, ['page'=> $i]), 'GET');
            $out['data'] = array_merge($out['data'], $response['data'],);
        }
        $doutata['page'] = 'All page';
        return $out;
    }
    public function getCustomFields()
    {
        $response = $this->user->api('order/custom-fields/', [], 'GET');
        return $response;
    }
    public function getType()
    {
        $response = $this->user->api('order/types/', [], 'GET');
        return $response;
    }
    public function create(
        $branch_id, // индитефикатор локации
        $order_type,
        $data = []
    ) {
        $json = array_merge($data, ['branch_id' => $branch_id, "order_type" => $order_type]);
        $response = $this->user->api('order/', $json, 'POST');
    }
    public function setStatus($order_id, $status_id)
    {
        $response = $this->user->api('order/', ['order_id' => $order_id, 'status_id' => $status_id], 'POST');
    }
}
