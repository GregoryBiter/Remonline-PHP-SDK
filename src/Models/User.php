<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class User extends Models
{
    private $endpoint = 'users';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $user_id): array
    {
        return $this->api->request($this->endpoint . '/' . $user_id, [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $user_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $user_id, $data, 'PATCH');
    }

    public function delete(int $user_id): array
    {
        return $this->api->request($this->endpoint . '/' . $user_id, [], 'DELETE');
    }

    public function getPermissions(int $user_id): array
    {
        return $this->api->request($this->endpoint . '/' . $user_id . '/permissions', [], 'GET');
    }

    public function setPermissions(int $user_id, array $permissions): array
    {
        return $this->api->request($this->endpoint . '/' . $user_id . '/permissions', $permissions, 'POST');
    }

    public function getRoles(): array
    {
        return $this->api->request('roles', [], 'GET');
    }

    public function assignRole(int $user_id, int $role_id): array
    {
        return $this->api->request($this->endpoint . '/' . $user_id . '/roles', ['role_id' => $role_id], 'POST');
    }

    public function removeRole(int $user_id, int $role_id): array
    {
        return $this->api->request($this->endpoint . '/' . $user_id . '/roles/' . $role_id, [], 'DELETE');
    }

    public function getCurrentUser(): array
    {
        return $this->api->request('user', [], 'GET');
    }

    public function updateProfile(array $data): array
    {
        return $this->api->request('user', $data, 'PATCH');
    }
}
