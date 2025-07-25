<?php

namespace Gbit\Remonline;

use Exception;

/**
 * Custom exception class for RemOnline API errors
 */
class RemonlineApiException extends Exception
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

    /**
     * Create a new API exception
     *
     * @param string $message Error message
     * @param int $httpCode HTTP status code
     * @param array $errorDetails Error details from API response
     * @param string $apiUrl API endpoint URL
     * @param array $requestData Original request parameters
     * @param Exception|null $previous Previous exception
     */
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

    /**
     * Get error details from API response
     *
     * @return array
     */
    public function getErrorDetails(): array
    {
        return $this->errorDetails;
    }

    /**
     * Get HTTP status code
     *
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * Get API endpoint URL
     *
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * Get original request data
     *
     * @return array
     */
    public function getRequestData(): array
    {
        return $this->requestData;
    }

    /**
     * Check if this is a specific API error
     *
     * @param string $errorCode Error code to check
     * @return bool
     */
    public function hasErrorCode(string $errorCode): bool
    {
        return isset($this->errorDetails['error']) && $this->errorDetails['error'] === $errorCode;
    }

    /**
     * Check if this is a validation error (HTTP 400 or 422)
     *
     * @return bool
     */
    public function isValidationError(): bool
    {
        return $this->httpCode === 422 || $this->httpCode === 400;
    }

    /**
     * Check if this is an authentication error (HTTP 401)
     *
     * @return bool
     */
    public function isAuthenticationError(): bool
    {
        return $this->httpCode === 401;
    }

    /**
     * Check if this is an authorization error (HTTP 403)
     *
     * @return bool
     */
    public function isAuthorizationError(): bool
    {
        return $this->httpCode === 403;
    }

    /**
     * Check if this is a not found error (HTTP 404)
     *
     * @return bool
     */
    public function isNotFoundError(): bool
    {
        return $this->httpCode === 404;
    }

    /**
     * Check if this is a rate limit error (HTTP 429)
     *
     * @return bool
     */
    public function isRateLimitError(): bool
    {
        return $this->httpCode === 429;
    }

    /**
     * Get a user-friendly error message
     *
     * @return string
     */
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

    /**
     * Get validation errors in a readable format
     *
     * @return string
     */
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

    /**
     * Check if the exception contains validation errors
     *
     * @return bool
     */
    public function hasValidationErrors(): bool
    {
        return isset($this->errorDetails['message']['validation']) ||
               isset($this->errorDetails['validation']) ||
               isset($this->errorDetails['errors']);
    }

    /**
     * Get validation errors array
     *
     * @return array
     */
    public function getValidationErrors(): array
    {
        // RemOnline API может возвращать ошибки в разных форматах
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

    /**
     * Get specific field validation errors
     *
     * @param string $field
     * @return array
     */
    public function getFieldErrors(string $field): array
    {
        $validationErrors = $this->getValidationErrors();
        
        if (isset($validationErrors[$field])) {
            return is_array($validationErrors[$field]) ? $validationErrors[$field] : [$validationErrors[$field]];
        }

        return [];
    }

    /**
     * Check if a specific field has validation errors
     *
     * @param string $field
     * @return bool
     */
    public function hasFieldError(string $field): bool
    {
        return !empty($this->getFieldErrors($field));
    }

    /**
     * Get all validation errors as a flat array
     *
     * @return array
     */
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

    /**
     * Format field error message
     *
     * @param string $field
     * @param string $error
     * @return string
     */
    private function formatFieldError(string $field, string $error): string
    {
        // Переводим некоторые названия полей на русский
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
