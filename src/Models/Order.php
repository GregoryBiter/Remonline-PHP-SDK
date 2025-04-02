<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Order extends Models
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
    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }
    

    public function getOrder(array $arr = [], bool $getAllPage = false): array
    {
        return $this->api->getData('order/', $arr, $getAllPage);
    }

    public function getCustomFields(): array
    {
        return $this->api->getData('order/custom-fields/', [], true);
    }
    public function getType(): array
    {
        return $this->api->getData('order/types/', [], true);
    }
    public function create(
        $branch_id, // индитефикатор локации
        $order_type,
        $data = []
    ): array {
        $json = array_merge($data, ['branch_id' => $branch_id, "order_type" => $order_type]);
        return $response = $this->api->request('order/', $json, 'POST');
    }
    public function setStatus($order_id, $status_id): array
    {
        return $response = $this->api->request('order/', ['order_id' => $order_id, 'status_id' => $status_id], 'POST');
    }
}
