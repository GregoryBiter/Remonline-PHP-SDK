<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Product extends Models
{

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }
    

    public function getProducts(array $arr = [], bool $getAllPage = false): array
    {
        return $this->api->getData('products/', $arr, $getAllPage);
    }

    public function getProductsbyID(int $product_id): array
    {
        return $this->api->getData('products/'. $product_id, [], true);
    }
    public function getProductCategoryes(): array
    {
        return $this->api->getData('warehouse/categories/', [], true);
    }
}
