<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Report extends Models
{
    private $endpoint = 'reports';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function getFinancial(array $params = []): array
    {
        return $this->api->request($this->endpoint . '/financial', $params, 'GET');
    }

    public function getSales(array $params = []): array
    {
        return $this->api->request($this->endpoint . '/sales', $params, 'GET');
    }

    public function getOrders(array $params = []): array
    {
        return $this->api->request($this->endpoint . '/orders', $params, 'GET');
    }

    public function getProducts(array $params = []): array
    {
        return $this->api->request($this->endpoint . '/products', $params, 'GET');
    }

    public function getServices(array $params = []): array
    {
        return $this->api->request($this->endpoint . '/services', $params, 'GET');
    }

    public function getEmployees(array $params = []): array
    {
        return $this->api->request($this->endpoint . '/employees', $params, 'GET');
    }

    public function getWarehouse(array $params = []): array
    {
        return $this->api->request($this->endpoint . '/warehouse', $params, 'GET');
    }

    public function getCustom(string $report_type, array $params = []): array
    {
        return $this->api->request($this->endpoint . '/' . $report_type, $params, 'GET');
    }

    public function export(string $report_type, array $params = [], string $format = 'xlsx'): array
    {
        $params['format'] = $format;
        return $this->api->request($this->endpoint . '/' . $report_type . '/export', $params, 'GET');
    }
}
