<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class SMS extends Models
{
    private $endpoint = 'sms';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $sms_id): array
    {
        return $this->api->request($this->endpoint . '/' . $sms_id, [], 'GET');
    }

    public function send(array $data): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function getTemplates(): array
    {
        return $this->api->request($this->endpoint . '/templates', [], 'GET');
    }

    public function createTemplate(array $data): array
    {
        return $this->api->request($this->endpoint . '/templates', $data, 'POST');
    }

    public function updateTemplate(int $template_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/templates/' . $template_id, $data, 'PATCH');
    }

    public function deleteTemplate(int $template_id): array
    {
        return $this->api->request($this->endpoint . '/templates/' . $template_id, [], 'DELETE');
    }

    public function getStatus(int $sms_id): array
    {
        return $this->api->request($this->endpoint . '/' . $sms_id . '/status', [], 'GET');
    }

    public function getBalance(): array
    {
        return $this->api->request($this->endpoint . '/balance', [], 'GET');
    }

    public function getStatistics(array $params = []): array
    {
        return $this->api->request($this->endpoint . '/statistics', $params, 'GET');
    }
}
