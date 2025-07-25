<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

/**
 * Класс для работы с сущностью "Смета" в API RemOnline.
 */
class Estimate extends Models
{
    /**
     * @var string $endpoint Конечная точка API для работы со сметами.
     */
    private $endpoint = 'estimates';

    /**
     * @var array $map Карта данных (не используется в текущей реализации).
     */
    private $map = [];

    /**
     * Конструктор класса Estimate.
     *
     * @param RemonlineClient $api Экземпляр клиента API.
     */
    public function __construct(RemonlineClient $api)
    {
        parent::__construct($api);
    }

    /**
     * Получить список всех возможных статусов, которые можно присвоить смете.
     *
     * @return array Ответ API с доступными статусами.
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
     * - int[] $types Список идентификаторов типов смет.
     * - int[] $branches Список идентификаторов локаций.
     * - int[] $ids Список идентификаторов смет.
     * - string[] $id_labels Массив номеров документов смет.
     * - int[] $statuses Массив идентификаторов статусов смет.
     * - int[] $managers Массив идентификаторов сотрудников.
     * - int[] $clients_ids Список идентификаторов клиентов.
     * - string[] $client_names Список имен клиентов.
     * - string[] $client_phones Список номеров телефонов клиентов.
     * - string[] $created_at Фильтр по дате создания (ISO 8601).
     * - string[] $modified_at Фильтр по дате изменения (ISO 8601).
     * - string[] $scheduled_for Фильтр по дате и времени "Запланировано на" (ISO 8601).
     * @param bool $getAllPage Если true, возвращает все страницы.
     * @return array Ответ API.
     */
    public function getEstimate(array $arr = [], bool $getAllPage = false): array
    {
        return $this->response(
            $this->api->getData($this->endpoint, $arr, $getAllPage)
        );
    }

    /**
     * Получить смету по её идентификатору.
     *
     * @param int $estimate_id Идентификатор сметы.
     * @return array Ответ API с данными сметы.
     */
    public function getEstimateById($estimate_id): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}", [], 'GET');
    }

    /**
     * Создать новую смету.
     *
     * @param array $data Данные для создания сметы. Обязательные параметры:
     * - int $branch_id Идентификатор локации.
     * - int $order_type_id Идентификатор типа заказа.
     * - int $client_id Идентификатор клиента.
     * - string $name Название сметы.
     * - string $description Описание сметы.
     * - float $total Сумма сметы.
     * - string $currency Валюта сметы.
     * - string $created_at Дата создания сметы (ISO 8601).
     * - string $due_date Дата завершения сметы (ISO 8601).
     * - int[] $items Массив идентификаторов позиций.
     * - int[] $tags Массив идентификаторов тегов.
     * @return array Ответ API.
     * @throws \InvalidArgumentException Если обязательные параметры не указаны.
     */
    public function create(array $data = []): array
    {
        return $this->api->create($this->endpoint, $data, ['branch_id', 'order_type_id', 'client_id']);
    }

    /**
     * Обновить данные сметы.
     *
     * @param int $estimate_id Идентификатор сметы.
     * @param array $data Данные для обновления.
     * @return array Ответ API.
     */
    public function update(int $estimate_id, array $data): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}", array_merge(['estimate_id' => $estimate_id], $data), 'PATCH');
    }

    /**
     * Получить список позиций в смете.
     *
     * @param int $estimate_id Идентификатор сметы.
     * @return array Ответ API с позициями сметы.
     */
    public function getItems(int $estimate_id): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}/items", ['estimate_id' => $estimate_id], 'GET');
    }

    /**
     * Добавить позицию в смету.
     *
     * @param int $estimate_id Идентификатор сметы.
     * @param array $data Данные позиции.
     * @return array Ответ API.
     */
    public function addItem(int $estimate_id, array $data): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}/items", $data, 'POST');
    }

    /**
     * Обновить позицию в смете.
     *
     * @param int $estimate_id Идентификатор сметы.
     * @param int $item_id Идентификатор позиции.
     * @param array $data Данные для обновления позиции.
     * @return array Ответ API.
     */
    public function updateItem(int $estimate_id, int $item_id, array $data): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}/items/{$item_id}", $data, 'POST');
    }

    /**
     * Установить статус сметы.
     *
     * @param int $estimate_id Идентификатор сметы.
     * @param int $status_id Идентификатор статуса.
     * @param string $comment Комментарий.
     * @return array Ответ API.
     */
    public function setStatus(int $estimate_id, int $status_id, string $comment): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}/status", ['status_id' => $status_id, 'comment' => $comment], 'POST');
    }

    /**
     * Добавить комментарий к смете.
     *
     * @param int $estimate_id Идентификатор сметы.
     * @param string $comment Текст комментария.
     * @param bool $is_private Приватный ли комментарий.
     * @return array Ответ API.
     */
    public function addComment(int $estimate_id, string $comment, bool $is_private): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}/comments", ['comment' => $comment, 'is_private' => $is_private], 'POST');
    }

    /**
     * Получить публичную ссылку на смету.
     *
     * @param int $estimate_id Идентификатор сметы.
     * @return array Ответ API с публичной ссылкой.
     */
    public function getPublicUrl(int $estimate_id): array
    {
        return $this->api->request("{$this->endpoint}/{$estimate_id}/public_url", [], 'GET');
    }
}