<?php

namespace App\DataExporter;

use App\DataExporter\Contract\FormatInterface;

class Exporter
{
    /**
     * @param string $filename
     * @param array $data
     * @param FormatInterface $format
     * @param array $formatterOptions
     * @return bool
     */
    public static function export(string $filename, array $data, FormatInterface $format, array $formatterOptions = [])
    {
        $handle = fopen($filename, 'w');

        $text = $format::format($data, $formatterOptions);
        fwrite($handle, $text);
        fclose($handle);
        chmod($filename, '0775');

        return true;
    }

    public static function download(string $filename)
    {
        if (file_exists($filename)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            flush(); // Flush system output buffer
            readfile($filename);
            exit;
        }
    }
}