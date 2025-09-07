<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Task extends Models
{
    private $endpoint = 'tasks';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $task_id): array
    {
        return $this->api->request($this->endpoint . '/' . $task_id, [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $task_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $task_id, $data, 'PATCH');
    }

    public function delete(int $task_id): array
    {
        return $this->api->request($this->endpoint . '/' . $task_id, [], 'DELETE');
    }

    public function complete(int $task_id): array
    {
        return $this->api->request($this->endpoint . '/' . $task_id . '/complete', [], 'POST');
    }

    public function addComment(int $task_id, string $comment): array
    {
        return $this->api->request($this->endpoint . '/' . $task_id . '/comments', ['comment' => $comment], 'POST');
    }
}
