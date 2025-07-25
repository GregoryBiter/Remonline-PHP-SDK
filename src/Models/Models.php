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

    public function __construct(RemonlineClient $api)
    {
        $this->api = $api;
    }

    // public function __set($name, $value)
    // {
    //     if (isset($this->map[$name])) {
    //         $this->data[$this->map[$name]] = $value;
    //     }
    // }

    // public function __get($name)
    // {
    //     if (isset($this->map[$name])) {
    //         return $this->data[$this->map[$name]] ?? null;
    //     }
    //     return null;
    // }
    protected function getAllMapFields()
    {
        $mapArray = [];

        foreach ($this->map as $key => $value) {
            if (isset($this->{$value}) && $this->{$value} != null){
                // Только если значение установлено и не null
                $mapArray[$key] = $this->{$value};
            }
        }

        return $mapArray;
    }
// TODO сделать фильтр работающий с массивом вида ['sort_dir' => 'asc', 'types' => 'electronics']
    public function filter(array $filter)
    {
        foreach ($filter as $key => $value) {
            if (isset($this->map[$key])) {
                $this->data[$this->map[$key]] = $value;
            }
        }
    }

    protected function response($response): array
    {
        $this->page = null;
        if (isset($response['count'])) {
            $this->meta['count'] = $response['count'];
        }
        if (isset($response['page'])) {
            $this->meta['page'] = $response['page'];
        }
        if (isset($response['page_size'])) {
            $this->meta['page_size'] = $response['page_size'];
        }
        if (isset($response['total_pages'])) {
            $this->meta['total_pages'] = $response['total_pages'];
        }
        if (isset($response['data'])) {
           
            $this->data = $response['data'];
            return $response['data'];
        }
        return $response;
    }

    /*
    [count] => 13266
    [page] => 1
    [page_size] => 50
    [total_pages] => 266*/
    public function meta($key = null)
    {
        if ($key) {
            return $this->meta[$key];
        }
        return $this->meta;
    }

    public function page($page)
    {
        $this->page = $page;
        return $this;
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
