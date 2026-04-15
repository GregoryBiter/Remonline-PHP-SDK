<?php

namespace Gbit\Roapp\Models;

use Gbit\Roapp\RoappClient;

abstract class Models
{
    protected $api;

    protected $data = [];
    protected $meta = [];

    protected $page = null;
    protected $pageSize = null;
    protected $offset = null;

    public function __construct(RoappClient $api)
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
    * @throws \Gbit\Roapp\RoappApiException
     */
    protected function safeRequest(string $endpoint, array $params = [], string $method = 'GET'): array
    {
        try {
            return $this->api->request($endpoint, $params, $method);
        } catch (\Gbit\Roapp\RoappApiException $e) {
            // Log detailed error information
            $this->logValidationError($e, $endpoint, $params, $method);
            
            // Re-throw for caller to handle
            throw $e;
        }
    }

    /**
     * Log validation error details
     * 
     * @param \Gbit\Roapp\RoappApiException $exception
     * @param string $endpoint
     * @param array $params
     * @param string $method
     */
    private function logValidationError(\Gbit\Roapp\RoappApiException $exception, string $endpoint, array $params, string $method): void
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
            
            \Gbit\Roapp\RoappClient::pushLogs($logData, true);
        }
    }

    /**
     * Extract missing required fields from validation error
     * 
    * @param \Gbit\Roapp\RoappApiException $exception
     * @return array
     */
    private function getMissingRequiredFields(\Gbit\Roapp\RoappApiException $exception): array
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
    * @param \Gbit\Roapp\RoappApiException $exception
     * @return array
     */
    public function getValidationErrorSummary(\Gbit\Roapp\RoappApiException $exception): array
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
