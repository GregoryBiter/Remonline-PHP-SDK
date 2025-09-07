<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Setting extends Models
{
    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }
    
    public function getCompanySetting(): array
    {
        return $this->api->request('settings/company', [], 'GET');
    }

    public function getLocations(): array
    {
        return $this->api->request('branches', [], 'GET');
    }

    public function getAdCampaigns(): array
    {
        return $this->api->request('marketing/campaigns', [], 'GET');
    }

    public function getPrices(): array
    {
        return $this->api->request('margins', [], 'GET');
    }

    public function getEmployees(): array
    {
        return $this->api->request('employees', [], 'GET');
    }

    public function getOrderTypes(): array
    {
        return $this->api->request('orders/types', [], 'GET');
    }

    public function getOrderCustomFields(): array
    {
        return $this->api->request('orders/custom-fields', [], 'GET');
    }

    public function getBookList(): array
    {
        return $this->api->request('book/list', [], 'GET');
    }
}
