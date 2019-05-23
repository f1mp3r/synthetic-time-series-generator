<?php

namespace App\DataExporter\Format;

use App\DataExporter\Contract\FormatInterface;

class HtmlFormat implements FormatInterface
{
    public static function format(array $data = [], array $options = []): ?string
    {
        $table = '<table class="table table-hover">';
        $useHeader = isset($options['header']) && $options['header'] == true;

        $first = true;
        foreach ($data as $row) {
            $table .= $first ? ($useHeader ? '<thead>' : '<tbody>') : '';
            $table .= '<tr>';

            foreach ($row as $column) {
                $table .= $first && $useHeader ? '<th>' : '<td>';

                $table .= $column;

                $table .= $first && $useHeader ? '</th>' : '</td>';
            }

            $table .= '</tr>';
            $table .= $useHeader && $first ? '</thead>' : '';
            $first = false;
        }

        $table .= '</tbody>';
        $table .= '</table>';

        return $table;
    }
}