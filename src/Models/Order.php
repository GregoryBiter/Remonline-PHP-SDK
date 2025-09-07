<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

class Order extends Models
{

    private $endpoint = 'orders';
    private $map = [

    ];
    /**
     * Конструктор класса Order
     *
     * @param RemonlineClient $api Экземпляр клиента Remonline
     */
    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    /**
     * Получить список всех возможных статусов, которые можно присвоить заказу.
     *
     * @return array Массив статусов заказа
     */
    public function getStatuses(): array
    {
        return $this->api->request('statuses/'.$this->endpoint, [], 'GET');
    }

    /**
     * Получить список всех заказов.
     *
     * @param array $arr Дополнительные параметры фильтрации (см. документацию API)
     * @return array Массив заказов
     */
    public function get(array $arr = []): array
    {
        return $this->api->request($this->endpoint, $arr, 'GET');
    }

    /**
     * Получить заказ по ID
     *
     * @param int $order_id Идентификатор заказа
     * @return array Массив с данными заказа
     */
    public function getById(int $order_id): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}", [], 'GET');
    }

    /**
     * Создать новый заказ.
     *
     * @param array $data Данные для создания заказа. Поддерживаемые ключи:
     * <ul>
     *   <li>branch_id (int): Идентификатор локации (обязательно)</li>
     *   <li>order_type_id (int): Идентификатор типа заказа (обязательно)</li>
     *   <li>client_id (int): Идентификатор клиента (обязательно)</li>
     *   <li>name (string): Название заказа</li>
     *   <li>description (string): Описание заказа</li>
     *   <li>total (float): Сумма заказа</li>
     *   <li>currency (string): Валюта заказа</li>
     *   <li>created_at (string): Дата создания (ISO 8601)</li>
     *   <li>due_date (string): Дата завершения (ISO 8601)</li>
     *   <li>items (int[]): Массив идентификаторов позиций</li>
     *   <li>tags (int[]): Массив идентификаторов тегов</li>
     * </ul>
     * @return array Массив с данными созданного заказа
     * @throws \InvalidArgumentException Если обязательные параметры не указаны
     */
    public function create(array $data = []): array
    {
        return $this->api->request($this->endpoint, $data, 'POST');
    }

    /**
     * Обновить заказ по ID
     *
     * @param int $order_id Идентификатор заказа
     * @param array $data Данные для обновления заказа. Поддерживаемые ключи аналогичны create()
     * @return array Массив с обновлёнными данными заказа
     */
    public function update(int $order_id, array $data): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}", array_merge(['order_id' => $order_id], $data), 'PATCH');
    }

    /**
     * Получить список позиций в заказе
     *
     * @param int $order_id Идентификатор заказа
     * @return array Массив позиций заказа
     */
    public function getItems(int $order_id): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}/items", ['order_id' => $order_id], 'GET');
    }
    /**
     * Добавить позицию в заказ
     *
     * @param int $order_id Идентификатор заказа
     * @param array $data Данные позиции. Поддерживаемые ключи:
     * <ul>
     *   <li>assignee_id (int): ID назначенного сотрудника</li>
     *   <li>quantity (float): Количество</li>
     *   <li>price (float): Цена за единицу</li>
     *   <li>cost (float): Себестоимость единицы</li>
     *   <li>discount (array|object): Скидка (объект или массив)</li>
     *   <li>warranty (array|object): Гарантия (объект или массив)</li>
     *   <li>tax_ids (int[]): Массив ID налогов</li>
     *   <li>comment (string): Текст комментария</li>
     * </ul>
     * @return array Массив с результатом добавления
     */
    public function addItem(int $order_id, array $data): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}/items", $data, 'POST');
    }

    /**
     * Обновить позицию заказа
     *
     * @param int $order_id Идентификатор заказа
     * @param int $item_id Идентификатор позиции
     * @param array $data Данные для обновления позиции. Поддерживаемые ключи аналогичны addItem()
     * @return array Массив с результатом обновления
     */
    public function updateItem(int $order_id, int $item_id, array $data): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}/items/{$item_id}", $data, 'POST');
    }

    /**
     * Установить статус заказа
     *
     * @param int $order_id Идентификатор заказа
     * @param int $status_id Идентификатор статуса
     * @param string $comment Комментарий к смене статуса
     * @return array Массив с результатом смены статуса
     */
    public function setStatus(int $order_id, int $status_id, string $comment): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}/status", ['status_id' => $status_id, 'comment' => $comment], 'POST');
    }

    /**
     * Добавить комментарий к заказу
     *
     * @param int $order_id Идентификатор заказа
     * @param string $comment Текст комментария
     * @param bool $is_private Приватность комментария
     * @return array Массив с результатом добавления комментария
     */
    public function addComment(int $order_id, string $comment, bool $is_private): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}/comments", ['comment' => $comment, 'is_private'=>$is_private ], 'POST');
    }

    /**
     * Получить публичную ссылку на заказ
     *
     * @param int $order_id Идентификатор заказа
     * @return array Массив с публичной ссылкой
     */
    public function getPublicUrl(int $order_id): array
    {
        return $this->api->request("{$this->endpoint}/{$order_id}/public_url", [], 'GET');
    }

}
