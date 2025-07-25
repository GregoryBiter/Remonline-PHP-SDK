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
        return $this->api->getData('settings/company', []);
    }

    public function getLocations(): array
    {
        return $this->response(
            $this->api->getData('branches/', [])
        );
    }

    public function getAdCampaigns(): array
    {
        return $this->response($this->api->getData('marketing/campaigns/', []));
    }
    public function getPrices(): array
    {
        return $this->api->getData('margins/', []);
    }

    public function getEmployees(): array
    {
        return $this->api->getData('employees/', []);
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
        return $this->api->getData('book/list/', []);
    }


}
