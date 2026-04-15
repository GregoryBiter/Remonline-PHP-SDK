<?php

namespace Gbit\Roapp\Helpers;

use Gbit\Roapp\RoappApiException;

/**
 * Utility class for handling Roapp API validation errors
 */
class ValidationErrorHandler
{
    /**
     * Extract validation errors and format them for display
     * 
     * @param RoappApiException $exception
     * @param array $options Formatting options
     * @return array
     */
    public static function formatValidationErrors(RoappApiException $exception, array $options = []): array
    {
        $defaultOptions = [
            'include_field_names' => true,
            'translate_fields' => true,
            'group_by_field' => false,
            'include_suggestions' => true
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        if (!$exception->isValidationError()) {
            return [
                'is_validation_error' => false,
                'message' => $exception->getUserFriendlyMessage(),
                'errors' => []
            ];
        }
        
        $validationErrors = $exception->getValidationErrors();
        $formattedErrors = [];
        
        foreach ($validationErrors as $field => $errors) {
            $fieldErrors = is_array($errors) ? $errors : [$errors];
            
            foreach ($fieldErrors as $error) {
                $formattedError = [
                    'field' => $field,
                    'field_translated' => $options['translate_fields'] ? self::translateField($field) : $field,
                    'message' => $error,
                    'suggestion' => $options['include_suggestions'] ? self::getSuggestion($field, $error) : null
                ];
                
                if ($options['group_by_field']) {
                    $formattedErrors[$field][] = $formattedError;
                } else {
                    $formattedErrors[] = $formattedError;
                }
            }
        }
        
        return [
            'is_validation_error' => true,
            'message' => $exception->getValidationErrorsMessage(),
            'errors' => $formattedErrors,
            'missing_required_fields' => self::extractMissingFields($validationErrors),
            'suggestions' => self::getGeneralSuggestions($validationErrors)
        ];
    }
    
    /**
     * Translate field names to Russian
     * 
     * @param string $field
     * @return string
     */
    public static function translateField(string $field): string
    {
        $translations = [
            'will_done_at' => 'Дата выполнения',
            'malfunction' => 'Неисправность',
            'ad_campaign_id' => 'Рекламная кампания',
            'contact_name' => 'Имя контакта',
            'contact_phone' => 'Телефон контакта',
            'description' => 'Описание',
            'client_id' => 'ID клиента',
            'leadtype_id' => 'Тип лида',
            'branch_id' => 'Филиал',
            'order_type_id' => 'Тип заказа',
            'warranty_status' => 'Статус гарантии',
            'device_model' => 'Модель устройства',
            'device_brand' => 'Бренд устройства',
            'device_serial' => 'Серийный номер',
            'custom_fields|group|kindof_good|brand|model|serial' => 'Дополнительные поля устройства'
        ];
        
        return $translations[$field] ?? $field;
    }
    
    /**
     * Get suggestion for field error
     * 
     * @param string $field
     * @param string $error
     * @return string|null
     */
    public static function getSuggestion(string $field, string $error): ?string
    {
        $suggestions = [
            'will_done_at' => 'Укажите дату в формате YYYY-MM-DD HH:MM:SS (например: ' . date('Y-m-d H:i:s', strtotime('+3 days')) . ')',
            'malfunction' => 'Опишите неисправность устройства (например: "Разбитый экран", "Не включается", "Проблемы с батареей")',
            'ad_campaign_id' => 'Укажите ID существующей рекламной кампании. Получить список можно через API: /ad_campaigns/',
            'contact_name' => 'Укажите имя и фамилию клиента',
            'contact_phone' => 'Укажите телефон в международном формате (например: +380123456789)',
            'client_id' => 'Укажите ID существующего клиента или создайте нового через API: /clients/',
            'leadtype_id' => 'Укажите ID типа лида. Получить список можно через API: /lead/types/',
            'branch_id' => 'Укажите ID филиала. Получить список можно через API: /branches/',
        ];
        
        if (isset($suggestions[$field])) {
            return $suggestions[$field];
        }
        
        // Общие предложения на основе типа ошибки
        if (strpos($error, 'Необходимо заполнить') !== false || strpos($error, 'required') !== false) {
            return 'Поле обязательно для заполнения';
        }
        
        if (strpos($error, 'invalid') !== false || strpos($error, 'неверный') !== false) {
            return 'Проверьте формат значения';
        }
        
        return null;
    }
    
    /**
     * Extract missing required fields
     * 
     * @param array $validationErrors
     * @return array
     */
    public static function extractMissingFields(array $validationErrors): array
    {
        $missingFields = [];
        
        foreach ($validationErrors as $field => $errors) {
            $fieldErrors = is_array($errors) ? $errors : [$errors];
            
            foreach ($fieldErrors as $error) {
                if (strpos($error, 'Необходимо заполнить') !== false || 
                    strpos($error, 'required') !== false) {
                    $missingFields[] = [
                        'field' => $field,
                        'field_translated' => self::translateField($field),
                        'suggestion' => self::getSuggestion($field, $error)
                    ];
                }
            }
        }
        
        return $missingFields;
    }
    
    /**
     * Get general suggestions for common error patterns
     * 
     * @param array $validationErrors
     * @return array
     */
    public static function getGeneralSuggestions(array $validationErrors): array
    {
        $suggestions = [];
        
        // Проверяем наличие custom_fields ошибок
        foreach ($validationErrors as $field => $errors) {
            if (strpos($field, 'custom_fields') !== false) {
                $suggestions[] = 'Для создания заказа необходимо заполнить дополнительные поля устройства. Проверьте настройки филиала в Roapp.';
                break;
            }
        }
        
        // Проверяем наличие дат
        if (isset($validationErrors['will_done_at'])) {
            $suggestions[] = 'Дата выполнения должна быть в будущем и в корректном формате.';
        }
        
        // Проверяем ID полей
        $idFields = ['ad_campaign_id', 'leadtype_id', 'branch_id', 'client_id'];
        foreach ($idFields as $idField) {
            if (isset($validationErrors[$idField])) {
                $suggestions[] = 'Проверьте корректность ID в полях связей. Используйте соответствующие API методы для получения списка доступных значений.';
                break;
            }
        }
        
        return array_unique($suggestions);
    }
    
    /**
     * Generate console-friendly error report
     * 
     * @param RoappApiException $exception
     * @return string
     */
    public static function generateConsoleReport(RoappApiException $exception): string
    {
        if (!$exception->isValidationError()) {
            return "Ошибка API: " . $exception->getUserFriendlyMessage();
        }
        
        $report = "┌─ ОШИБКА ВАЛИДАЦИИ ROAPP API ───────────────────────────┐\n";
        $report .= sprintf("│ HTTP Код: %-48s │\n", $exception->getHttpCode());
        $report .= "├────────────────────────────────────────────────────────────┤\n";
        
        $validationErrors = $exception->getValidationErrors();
        foreach ($validationErrors as $field => $errors) {
            $fieldErrors = is_array($errors) ? $errors : [$errors];
            $translatedField = self::translateField($field);
            
            $report .= sprintf("│ %-58s │\n", "Поле: " . $translatedField . " (" . $field . ")");
            
            foreach ($fieldErrors as $error) {
                $lines = self::wrapText("  ✗ " . $error, 56);
                foreach ($lines as $line) {
                    $report .= sprintf("│ %-58s │\n", $line);
                }
            }
            
            $suggestion = self::getSuggestion($field, $fieldErrors[0]);
            if ($suggestion) {
                $lines = self::wrapText("  💡 " . $suggestion, 56);
                foreach ($lines as $line) {
                    $report .= sprintf("│ %-58s │\n", $line);
                }
            }
            
            $report .= "├────────────────────────────────────────────────────────────┤\n";
        }
        
        $report .= "└────────────────────────────────────────────────────────────┘";
        
        return $report;
    }
    
    /**
     * Wrap text to specified width
     * 
     * @param string $text
     * @param int $width
     * @return array
     */
    private static function wrapText(string $text, int $width): array
    {
        $lines = [];
        $words = explode(' ', $text);
        $currentLine = '';
        
        foreach ($words as $word) {
            if (mb_strlen($currentLine . ' ' . $word) <= $width) {
                $currentLine = trim($currentLine . ' ' . $word);
            } else {
                if (!empty($currentLine)) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            }
        }
        
        if (!empty($currentLine)) {
            $lines[] = $currentLine;
        }
        
        return $lines;
    }
}
