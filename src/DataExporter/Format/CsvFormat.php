<?php

namespace App\DataExporter\Format;

use App\DataExporter\Contract\FormatInterface;
use Exception;

class CsvFormat implements FormatInterface
{
    /**
     * @var array
     */
    private static $CSV_DEFAULTS = [
        'delimiter' => ',',
        'enclosure' => '"',
        'escape_char' => "\\"
    ];

    /**
     * Exports table to csv. If file exists, it will overwrite it
     * $options can contain any of the following keys:
     * - delimiter - for the csv delimiter
     * - enclosure - for the csv enclosure
     * - escape_char - for the csv escape_char
     *
     * @param array $data
     * @param array $options
     * @return string
     * @throws Exception
     */
    public static function format(array $data = [], array $options = []): ?string
    {
        $config = self::mergeOptions($options);

        $handle = fopen('php://temp', 'rw');

        try {
            foreach ($data as $row) {
                $inserted = fputcsv(
                    $handle,
                    $row,
                    $config['delimiter'],
                    $config['enclosure'],
                    $config['escape_char']
                );

                if (!$inserted) {
                    throw new Exception('Couldn\'t add row: ' . (error_get_last() ? error_get_last()['message'] : ''));
                }
            }
        } catch (Exception $exception) {
            fclose($handle);

            throw $exception;
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    /**
     * @param array $options
     * @return array
     */
    private static function mergeOptions(array $options)
    {
        $merged = [];

        foreach (self::$CSV_DEFAULTS as $optionKey => $optionValue) {
            $merged[$optionKey] = $options[$optionKey] ?? $optionValue;
        }

        return $merged;
    }
}