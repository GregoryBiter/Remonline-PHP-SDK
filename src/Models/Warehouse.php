<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Warehouse extends Models
{

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(?int $branch_id = null, bool $getAllPage = false): array
    {
        return $this->api->getData('warehouse/', ['branch_id' => $branch_id], $getAllPage);
    }


    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function goods(int $warehouse_id, array $categories = [], bool $exclude_zero_residue = false, ?int $page = null, bool $getAllPage = false): array
    {
        return $this->api->getData(
            'warehouse/goods/' . $warehouse_id,
            [
                'categories' => $categories,
                'exclude_zero_residue' => $exclude_zero_residue,
                'page' => $page
            ],
            $getAllPage
        );
    }




    // public function goods($warehouse_id, $categories = [], $exclude_zero_residue = false)
    // {
    //     $page = $this->page;
    //     $this->page = null;
    //     $in_data = array_merge($categories, ['page' => $this->page]);
    //     $in_data['exclude_zero_residue'] = $exclude_zero_residue;
    //     return $this->user->api('order/', array_merge($in_data, [
    //         'page' => $page
    //     ]), 'GET');
    // }



    public function getCategories()
    {
        $response = $this->api->request('warehouse/categories/', [], 'GET');
        return $response;
    }
    public function getWarehouse($branch_id = null)
    {
        $response = $this->api->request('warehouse/', ['$branch_id' => $branch_id], 'GET');
        return $response;
    }
    public function getPostings($warehouse_id = null, $created_at = null, $ids = null)
    {
        $in_data = [];
        if ($warehouse_id != null) {
            $in_data = array_merge($in_data, ['page' => $this->page]);
        }
        $in_data = array_merge($warehouse_id = [], ['page' => $this->page]);
        $response = $this->api->request('warehouse/', ['$branch_id' => $branch_id], 'GET');
        return $response;
    }
}
