<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\Api;

class Warehouse extends Models
{
    public function get($branch_id = null)
    {
        $in_data = [];
        $page = $this->page;
        $this->page = null;
        if ($branch_id != null) {
            $in_data['branch_id'] = $branch_id;
        }
        return $this->user->api('order/', array_merge($in_data, [
            'page' => $page
        ]), 'GET');
    }
    public function goods($warehouse_id, $categories = [], $exclude_zero_residue = false)
    {
        $page = $this->page;
        $this->page = null;
        $in_data = array_merge($categories, ['page' => $this->page]);
        $in_data['exclude_zero_residue'] = $exclude_zero_residue;
        return $this->user->api('order/', array_merge($in_data, [
            'page' => $page
        ]), 'GET');
    }



    public function getCategories()
    {
        $response = $this->user->api('warehouse/categories/', [], 'GET');
        return $response;
    }
    public function getWarehouse($branch_id = null)
    {
        $response = $this->user->api('warehouse/', ['$branch_id' => $branch_id], 'GET');
        return $response;
    }
    public function getPostings($warehouse_id = null, $created_at = null, $ids = null)
    {
        $in_data = [];
        if ($warehouse_id != null) {
            $in_data = array_merge($in_data, ['page' => $this->page]);
        }
        $in_data = array_merge($warehouse_id = [], ['page' => $this->page]);
        $response = $this->user->api('warehouse/', ['$branch_id' => $branch_id], 'GET');
        return $response;
    }
}
