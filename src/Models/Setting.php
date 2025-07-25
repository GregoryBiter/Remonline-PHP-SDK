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

    public function getLocations(){
        return $this->response(
            $this->api->getData('branches/', [])
        );
    }

    public function getAdCampaigns(){
        return $this->api->getData('marketing/campaigns/', []);
    }
    public function getPrices(){
        return $this->api->getData('margins/', []);
    }

    public function getEmployees(){
        return $this->api->getData('employees/', []);
    }

    public function getOrderTypes(){
        return $this->api->request('orders/types', [], 'GET');
    }

    public function getOrderCustomFields(){
        return $this->api->request('orders/custom-fields', [], 'GET');
    }


}
