<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Invoice extends Models
{
    private $endpoint = 'invoices';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $invoice_id): array
    {
        return $this->api->request($this->endpoint . '/' . $invoice_id, [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $invoice_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $invoice_id, $data, 'PATCH');
    }

    public function delete(int $invoice_id): array
    {
        return $this->api->request($this->endpoint . '/' . $invoice_id, [], 'DELETE');
    }

    public function getStatuses(): array
    {
        return $this->api->request('statuses/invoices', [], 'GET');
    }

    public function setStatus(int $invoice_id, int $status_id, string $comment = ''): array
    {
        return $this->api->request($this->endpoint . '/' . $invoice_id . '/status', [
            'status_id' => $status_id,
            'comment' => $comment
        ], 'POST');
    }

    public function getItems(int $invoice_id): array
    {
        return $this->api->request($this->endpoint . '/' . $invoice_id . '/items', [], 'GET');
    }

    public function addItem(int $invoice_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $invoice_id . '/items', $data, 'POST');
    }

    public function updateItem(int $invoice_id, int $item_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $invoice_id . '/items/' . $item_id, $data, 'PATCH');
    }

    public function send(int $invoice_id, array $data = []): array
    {
        return $this->api->request($this->endpoint . '/' . $invoice_id . '/send', $data, 'POST');
    }

    public function markAsPaid(int $invoice_id, array $payment_data = []): array
    {
        return $this->api->request($this->endpoint . '/' . $invoice_id . '/mark-paid', $payment_data, 'POST');
    }
}
