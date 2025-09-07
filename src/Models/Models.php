<?php

namespace Gbit\Remonline\Models;

use Gbit\Remonline\RemonlineClient;

abstract class Models
{
    protected $api;
    private $map = [];

    protected $data = [];
    protected $meta = [];

    protected $page = null;
    protected $pageSize = null;
    protected $offset = null;

    public function __construct(RemonlineClient $api)
    {
        $this->api = $api;
    }

    /**
     * Safe request execution with enhanced error handling
     * 
     * @param string $endpoint
     * @param array $params
     * @param string $method
     * @return array
     * @throws \Gbit\Remonline\RemonlineApiException
     */
    protected function safeRequest(string $endpoint, array $params = [], string $method = 'GET'): array
    {
        try {
            return $this->api->request($endpoint, $params, $method);
        } catch (\Gbit\Remonline\RemonlineApiException $e) {
            // Log detailed error information
            $this->logValidationError($e, $endpoint, $params, $method);
            
            // Re-throw for caller to handle
            throw $e;
        }
    }

    /**
     * Log validation error details
     * 
     * @param \Gbit\Remonline\RemonlineApiException $exception
     * @param string $endpoint
     * @param array $params
     * @param string $method
     */
    private function logValidationError(\Gbit\Remonline\RemonlineApiException $exception, string $endpoint, array $params, string $method): void
    {
        if ($exception->isValidationError()) {
            $logData = [
                'type' => 'validation_error',
                'endpoint' => $endpoint,
                'method' => $method,
                'sent_params' => $params,
                'validation_errors' => $exception->getValidationErrors(),
                'user_friendly_message' => $exception->getUserFriendlyMessage(),
                'missing_fields' => $this->getMissingRequiredFields($exception),
                'http_code' => $exception->getHttpCode()
            ];
            
            \Gbit\Remonline\RemonlineClient::pushLogs($logData, true);
        }
    }

    /**
     * Extract missing required fields from validation error
     * 
     * @param \Gbit\Remonline\RemonlineApiException $exception
     * @return array
     */
    private function getMissingRequiredFields(\Gbit\Remonline\RemonlineApiException $exception): array
    {
        $missingFields = [];
        $validationErrors = $exception->getValidationErrors();
        
        foreach ($validationErrors as $field => $errors) {
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    if (strpos($error, 'Необходимо заполнить') !== false || 
                        strpos($error, 'required') !== false) {
                        $missingFields[] = $field;
                    }
                }
            } elseif (strpos($errors, 'Необходимо заполнить') !== false || 
                      strpos($errors, 'required') !== false) {
                $missingFields[] = $field;
            }
        }
        
        return array_unique($missingFields);
    }

    /**
     * Get validation error summary for display
     * 
     * @param \Gbit\Remonline\RemonlineApiException $exception
     * @return array
     */
    public function getValidationErrorSummary(\Gbit\Remonline\RemonlineApiException $exception): array
    {
        if (!$exception->isValidationError()) {
            return [
                'is_validation_error' => false,
                'message' => $exception->getUserFriendlyMessage()
            ];
        }

        return [
            'is_validation_error' => true,
            'message' => $exception->getUserFriendlyMessage(),
            'field_errors' => $exception->getValidationErrors(),
            'missing_fields' => $this->getMissingRequiredFields($exception),
            'all_messages' => $exception->getAllValidationMessages()
        ];
    }

}
