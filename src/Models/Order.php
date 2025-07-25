<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Order extends Models
{

    private $endpoint = 'orders';
    private $map = [

    ];
    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    /**
     * Получить список всех возможных статусов, которые можно присвоить смете.
     */
    public function getStatuses(): array
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
    public function get(array $arr = [], bool $getAllPage = false): array
    {
        return $this->response(
            $this->api->getData($this->endpoint, $arr, $getAllPage)
        );
    }

    public function getById($order_id): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}", [], 'GET');
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
        return $this->api->create($this->endpoint, $data, ['branch_id', 'order_type_id', 'client_id']);
    }

    public function update(int $order_id, array $data): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}", array_merge(['order_id' => $order_id], $data), 'PATCH');
    }

    /**
     * Получить список позиций в смете.
     *
     * @param int $estimate_id Идентификатор сметы.
     * @return array Ответ API с позициями сметы.
     */
    public function getItems(int $order_id): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}/items", ['order_id' => $order_id], 'GET');
    }
    public function addItem(int $order_id, array $data): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}/items", $data, 'POST');
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
    public function updateItem(int $order_id, int $item_id, array $data): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}/items/{$item_id}", $data, 'POST');
    }

    /**
     * Summary of setStatus
     * @param int $order_id
     * @param int $status_id
     * @param string $comment
     * @return array
     */
    public function setStatus(int $order_id, int $status_id, string $comment): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}/status", ['status_id' => $status_id, 'comment' => $comment], 'POST');
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
    public function addComment(int $order_id, string $comment, bool $is_private): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}/comments", ['comment' => $comment, 'is_private'=>$is_private ], 'POST');
    }

    public function getPublicUrl(int $order_id): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}/public_url", [], 'GET');
    }

}
