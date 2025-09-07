<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Warehouse extends Models
{
    private $endpoint = 'warehouse';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(?int $branch_id = null): array
    {
        return $this->api->request($this->endpoint, ['branch_id' => $branch_id], 'GET');
    }

    public function goods(int $warehouse_id, array $categories = [], bool $exclude_zero_residue = false, ?int $page = null): array
    {
        return $this->api->request(
            $this->endpoint . '/goods/' . $warehouse_id,
            [
                'categories' => $categories,
                'exclude_zero_residue' => $exclude_zero_residue,
                'page' => $page
            ],
            'GET'
        );
    }

    public function getCategories(): array
    {
        return $this->api->request($this->endpoint . '/categories', [], 'GET');
    }

    public function getPostings(int $warehouse_id = null, string $created_at = null, array $ids = null): array
    {
        $params = [];
        if ($warehouse_id !== null) {
            $params['warehouse_id'] = $warehouse_id;
        }
        if ($created_at !== null) {
            $params['created_at'] = $created_at;
        }
        if ($ids !== null) {
            $params['ids'] = $ids;
        }
        
        return $this->api->request($this->endpoint . '/postings', $params, 'GET');
    }
}
