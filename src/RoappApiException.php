<?php

namespace Gbit\Roapp;

use Exception;

/**
 * Custom exception class for Roapp API errors
 */
class RoappApiException extends Exception
{
    /**
     * @var array Error details from API response
     */
    protected $errorDetails = [];

    /**
     * @var int HTTP status code
     */
    protected $httpCode;

    /**
     * @var array Original request parameters
     */
    protected $requestData = [];

    /**
     * @var string API endpoint URL
     */
    protected $apiUrl;

    public function __construct(
        string $message = '',
        int $httpCode = 0,
        array $errorDetails = [],
        string $apiUrl = '',
        array $requestData = [],
        Exception $previous = null
    ) {
        parent::__construct($message, $httpCode, $previous);

        $this->httpCode = $httpCode;
        $this->errorDetails = $errorDetails;
        $this->apiUrl = $apiUrl;
        $this->requestData = $requestData;
    }

    public function getErrorDetails(): array
    {
        return $this->errorDetails;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getRequestData(): array
    {
        return $this->requestData;
    }

    public function hasErrorCode(string $errorCode): bool
    {
        return isset($this->errorDetails['error']) && $this->errorDetails['error'] === $errorCode;
    }

    public function isValidationError(): bool
    {
        return $this->httpCode === 422 || $this->httpCode === 400;
    }

    public function isAuthenticationError(): bool
    {
        return $this->httpCode === 401;
    }

    public function isAuthorizationError(): bool
    {
        return $this->httpCode === 403;
    }

    public function isNotFoundError(): bool
    {
        return $this->httpCode === 404;
    }

    public function isRateLimitError(): bool
    {
        return $this->httpCode === 429;
    }

    public function getUserFriendlyMessage(): string
    {
        switch ($this->httpCode) {
            case 400:
                if ($this->isValidationError()) {
                    return $this->getValidationErrorsMessage();
                }
                return 'Неправильный запрос к API';
            case 401:
                return 'Неверный API ключ или токен доступа';
            case 403:
                return 'Недостаточно прав для выполнения операции';
            case 404:
                return 'Запрашиваемый ресурс не найден';
            case 422:
                return $this->getValidationErrorsMessage();
            case 429:
                return 'Превышен лимит запросов к API';
            case 500:
                return 'Внутренняя ошибка сервера';
            default:
                return $this->getMessage();
        }
    }

    public function getValidationErrorsMessage(): string
    {
        if (!$this->hasValidationErrors()) {
            return $this->getMessage();
        }

        $validationErrors = $this->getValidationErrors();
        $messages = [];

        foreach ($validationErrors as $field => $errors) {
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    $messages[] = $this->formatFieldError($field, $error);
                }
            } else {
                $messages[] = $this->formatFieldError($field, $errors);
            }
        }

        return 'Ошибки валидации: ' . implode('; ', $messages);
    }

    public function hasValidationErrors(): bool
    {
        return isset($this->errorDetails['message']['validation']) ||
               isset($this->errorDetails['validation']) ||
               isset($this->errorDetails['errors']);
    }

    public function getValidationErrors(): array
    {
        if (isset($this->errorDetails['message']['validation'])) {
            return $this->errorDetails['message']['validation'];
        }

        if (isset($this->errorDetails['validation'])) {
            return $this->errorDetails['validation'];
        }

        if (isset($this->errorDetails['errors'])) {
            return $this->errorDetails['errors'];
        }

        return [];
    }

    public function getFieldErrors(string $field): array
    {
        $validationErrors = $this->getValidationErrors();
        
        if (isset($validationErrors[$field])) {
            return is_array($validationErrors[$field]) ? $validationErrors[$field] : [$validationErrors[$field]];
        }

        return [];
    }

    public function hasFieldError(string $field): bool
    {
        return !empty($this->getFieldErrors($field));
    }

    public function getAllValidationMessages(): array
    {
        $validationErrors = $this->getValidationErrors();
        $messages = [];

        foreach ($validationErrors as $field => $errors) {
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    $messages[] = $this->formatFieldError($field, $error);
                }
            } else {
                $messages[] = $this->formatFieldError($field, $errors);
            }
        }

        return $messages;
    }

    private function formatFieldError(string $field, string $error): string
    {
        $fieldTranslations = [
            'will_done_at' => 'Дата выполнения',
            'malfunction' => 'Неисправность',
            'ad_campaign_id' => 'Рекламная кампания',
            'contact_name' => 'Имя контакта',
            'contact_phone' => 'Телефон контакта',
            'description' => 'Описание',
            'client_id' => 'ID клиента',
            'leadtype_id' => 'Тип лида',
            'branch_id' => 'Филиал',
        ];

        $fieldName = $fieldTranslations[$field] ?? $field;
        return $fieldName . ': ' . $error;
    }
}
