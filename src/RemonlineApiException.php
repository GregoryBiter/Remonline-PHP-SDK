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
     * Check if this is a validation error (HTTP 422)
     *
     * @return bool
     */
    public function isValidationError(): bool
    {
        return $this->httpCode === 422;
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
            case 401:
                return 'Неверный API ключ или токен доступа';
            case 403:
                return 'Недостаточно прав для выполнения операции';
            case 404:
                return 'Запрашиваемый ресурс не найден';
            case 422:
                return 'Ошибка валидации данных';
            case 429:
                return 'Превышен лимит запросов к API';
            case 500:
                return 'Внутренняя ошибка сервера';
            default:
                return $this->getMessage();
        }
    }
}
