<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Estimate extends Models
{

   
    private $map = [
        'sort_dir' => '',
        'types' => '',
        'branches' => '',
        'brands' => '',
        'ids' => '',
        'id_labels[]' => '',
        'statuses' => '',
        'managers' => '',
        'engineers' => '',
        'clients_ids' => '',
        'client_names' => '',
        'client_phones' => '',
        'created_at' => '',
        'done_at' => '',
        'modified_at' => '',
        'closed_at' => ''
    ];
    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    /**
     * Получить список всех возможных статусов, которые можно присвоить смете.
     */
    public function getStatus(): array
    {
        return $this->api->getData('statuses/estimates', [], true);
    }

    /**
     * Получить список всех смет.
     *
     * @param array $arr Дополнительные параметры:
     * - int $page Номер страницы (по умолчанию 1).
     * - int[] $types Список идентификаторов типов смет (аналогично типам заказов).
     * - int[] $branches Список идентификаторов локаций.
     * - int[] $ids Список идентификаторов смет.
     * - string[] $id_labels Массив номеров документов смет.
     * - int[] $statuses Массив идентификаторов статусов смет.
     * - int[] $managers Массив идентификаторов сотрудников.
     * - int[] $clients_ids Список идентификаторов клиентов.
     * - string[] $client_names Список имен клиентов.
     * - string[] $client_phones Список номеров телефонов клиентов.
     * - string[] $created_at Фильтр по дате создания (ISO 8601). Один элемент — начало диапазона, два элемента — диапазон.
     * - string[] $modified_at Фильтр по дате изменения (ISO 8601). Один элемент — начало диапазона, два элемента — диапазон.
     * - string[] $scheduled_for Фильтр по дате и времени "Запланировано на" (ISO 8601). Один элемент — начало диапазона, два элемента — диапазон.
     * @param bool $getAllPage Если true, возвращает все страницы.
     * @return array Ответ API.
     */
    public function getEstimate(array $arr = [], bool $getAllPage = false): array
    {
        return $this->api->getData('estimate/', $arr, $getAllPage);
    }

    public function getEstimateById($estimate_id): array
    {
        return $this->api->request('estimates/estimate_id', ['estimate_id' => $estimate_id], 'GET');
    }

    /**
     * Создать новую смету.
     *
     * @param array $data Данные для создания сметы. Обязательные и дополнительные параметры:
     * - int $branch_id Идентификатор локации (обязательно).
     * - int $order_type_id Идентификатор типа заказа (обязательно).
     * - int $client_id Идентификатор клиента (обязательно).
     * - string $name Название сметы.
     * - string $description Описание сметы.
     * - float $total Сумма сметы.
     * - string $currency Валюта сметы (например, "USD").
     * - string $created_at Дата создания сметы (ISO 8601).
     * - string $due_date Дата завершения сметы (ISO 8601).
     * - int[] $items Массив идентификаторов позиций, включённых в смету.
     * - int[] $tags Массив идентификаторов тегов, связанных со сметой.
     * @return array Ответ API.
     * @throws \InvalidArgumentException Если обязательные параметры не указаны.
     */
    public function create(array $data = []): array
    {
        // Используем универсальный метод из $this->api
        return $this->api->create('estimates/', $data, ['branch_id', 'order_type_id', 'client_id']);
    }

    public function update(int $estimate_id, array $data): array
    {
        return $this->api->request("estimates/{$estimate_id}", array_merge(['estimate_id' => $estimate_id], $data), 'PATCH');
    }

    /**
     * Получить список позиций в смете.
     *
     * @param int $estimate_id Идентификатор сметы.
     * @return array Ответ API с позициями сметы.
     */
    public function getItems(int $estimate_id): array
    {
        return $this->api->request("estimates/{$estimate_id}/items", ['estimate_id' => $estimate_id], 'GET');
    }
    public function addItem(int $estimate_id, array $data): array
    {
        return $this->api->request("estimates/{$estimate_id}", $data, 'POST');
    }
    /*
assignee_id
int32
Assigned Employee ID

quantity
float
Quantity

price
float
Price per unit

cost
float
Unit cost

discount
object

discount object
warranty
object

warranty object
tax_ids
array of int32s
Array of Tax ID


ADD int32
comment
string
Comment text
*/
    public function updateItem(int $estimate_id, int $item_id, array $data): array
    {
        return $this->api->request("estimates/{$estimate_id}/items/{$item_id}", $data, 'POST');
    }

    /**
     * Summary of setStatus
     * @param int $estimate_id
     * @param int $status_id
     * @param string $comment
     * @return array
     */
    public function setStatus(int $estimate_id, int $status_id, string $comment): array
    {
        return $this->api->request("estimates/{$estimate_id}/status", ['status_id' => $status_id, 'comment' => $comment], 'POST');
    }
    /*
    Path Params
    estimate_id
    int32
    required
    Estimate ID

    Body Params
    comment
    string
    required
    Comment

    is_private
    boolean
    Defaults to false
    Is this comment private?
    */
    public function addComment(int $estimate_id, string $comment, bool $is_private): array
    {
        return $this->api->request("estimates/{$estimate_id}/comments", ['comment' => $comment, 'is_private'=>$is_private ], 'POST');
    }

}
