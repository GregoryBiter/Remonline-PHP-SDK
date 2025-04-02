<?php

class CSVHandler
{
    private $filename;
    private $delimiter;

    /**
     * Конструктор класса CSVHandler
     *
     * @param string $filename Имя файла
     * @param string $delimiter Разделитель (по умолчанию ',')
     */
    public function __construct(string $filename, string $delimiter = ',')
    {
        $this->filename = $filename;
        $this->delimiter = $delimiter;

        // Проверяем, существует ли файл, если нет - создаем его
        if (!file_exists($this->filename)) {
            $file = fopen($this->filename, 'w');
            fclose($file);
        }
    }

    /**
     * Сохраняет данные в CSV файл
     *
     * @param array $data Данные для записи
     * @return void
     */
    public function saveDataToCSV(array $data): void
    {
        $file = fopen($this->filename, 'w');

        // Записываем заголовки
        if (!empty($data)) {
            fputcsv($file, array_keys($data[0]), $this->delimiter);
        }

        // Записываем данные
        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                if (is_array($value)) {
                    $row[$key] = json_encode($value);
                }
            }
            fputcsv($file, $row, $this->delimiter);
        }

        fclose($file);
    }

    /**
     * Читает данные из CSV файла
     *
     * @return array Данные из файла
     */
    public function readDataFromCSV(): array
    {
        $data = [];
        if (($file = fopen($this->filename, 'r')) !== false) {
            $headers = fgetcsv($file, 0, $this->delimiter);

            while (($row = fgetcsv($file, 0, $this->delimiter)) !== false) {
                $row = array_combine($headers, $row);
                foreach ($row as $key => $value) {
                    $decodedValue = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $row[$key] = $decodedValue;
                    }
                }
                $data[] = $row;
            }

            fclose($file);
        }

        return $data;
    }

    /**
     * Очищает CSV файл
     *
     * @return void
     */
    public function clearCSV(): void
    {
        $file = fopen($this->filename, 'w');
        fclose($file);
    }
}