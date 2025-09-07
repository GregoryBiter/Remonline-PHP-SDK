<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Estimate extends Models
{
    private $endpoint = 'estimates';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function getStatuses(): array
    {
        return $this->api->request('statuses/estimates', [], 'GET');
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getById(int $estimate_id): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}", [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $estimate_id, array $data): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}", $data, 'PATCH');
    }

    public function getItems(int $estimate_id): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}/items", [], 'GET');
    }

    public function addItem(int $estimate_id, array $data): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}/items", $data, 'POST');
    }

    public function updateItem(int $estimate_id, int $item_id, array $data): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}/items/{$item_id}", $data, 'POST');
    }

    public function setStatus(int $estimate_id, int $status_id, string $comment): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}/status", ['status_id' => $status_id, 'comment' => $comment], 'POST');
    }

    public function addComment(int $estimate_id, string $comment, bool $is_private): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}/comments", ['comment' => $comment, 'is_private' => $is_private], 'POST');
    }

    public function getPublicUrl(int $estimate_id): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}/public_url", [], 'GET');
    }
}