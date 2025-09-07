<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Booking extends Models
{
    private $endpoint = 'bookings';

    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    public function getByID(int $booking_id): array
    {
        return $this->api->request($this->endpoint . '/' . $booking_id, [], 'GET');
    }

    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    public function update(int $booking_id, array $data): array
    {
        return $this->api->request($this->endpoint . '/' . $booking_id, $data, 'PATCH');
    }

    public function delete(int $booking_id): array
    {
        return $this->api->request($this->endpoint . '/' . $booking_id, [], 'DELETE');
    }

    public function confirm(int $booking_id): array
    {
        return $this->api->request($this->endpoint . '/' . $booking_id . '/confirm', [], 'POST');
    }

    public function cancel(int $booking_id, string $reason = ''): array
    {
        return $this->api->request($this->endpoint . '/' . $booking_id . '/cancel', ['reason' => $reason], 'POST');
    }

    public function reschedule(int $booking_id, string $new_date, string $new_time): array
    {
        return $this->api->request($this->endpoint . '/' . $booking_id . '/reschedule', [
            'date' => $new_date,
            'time' => $new_time
        ], 'POST');
    }
}
