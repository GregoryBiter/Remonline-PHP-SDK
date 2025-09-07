<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Webhook extends Models
{
    private $endpoint = 'webhooks';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $webhook_id): array
    {
        return $this->api->request($this->endpoint . '/' . $webhook_id, [], 'GET');
    }

    public function create(array $data): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $webhook_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $webhook_id, $data, 'PATCH');
    }

    public function delete(int $webhook_id): array
    {
        return $this->api->request($this->endpoint . '/' . $webhook_id, [], 'DELETE');
    }

    public function test(int $webhook_id): array
    {
        return $this->api->request($this->endpoint . '/' . $webhook_id . '/test', [], 'POST');
    }

    public function getLogs(int $webhook_id, array $params = []): array
    {
        return $this->api->request($this->endpoint . '/' . $webhook_id . '/logs', $params, 'GET');
    }

    public function getEvents(): array
    {
        return $this->api->request($this->endpoint . '/events', [], 'GET');
    }

    public function enable(int $webhook_id): array
    {
        return $this->api->request($this->endpoint . '/' . $webhook_id . '/enable', [], 'POST');
    }

    public function disable(int $webhook_id): array
    {
        return $this->api->request($this->endpoint . '/' . $webhook_id . '/disable', [], 'POST');
    }
}
