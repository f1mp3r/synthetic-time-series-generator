<?php

use App\DataExporter\Exporter;
use App\DataExporter\Format\CsvFormat;
use App\DataExporter\Format\HtmlFormat;
use App\DataGenerator\{Table, Generator};

require_once '../vendor/autoload.php';
ini_set('precision', 16);
error_reporting(E_ALL ^ E_NOTICE);

$generator = new Generator(
    isset($_GET['data_table']) && isset($_GET['classification'])
        ? Table::fromExternal($_GET['data_table'], $_GET['classification'])
        : Table::fromArray(require('../common/data_constants.php'))
);

$numberOfTables = $_GET['tables'] ?? 1;
$export = isset($_GET['download']) && $_GET['download'] == 1;

$allTables = [];
foreach (range(1, $numberOfTables) as $item) {
    try {
        $tableData = $generator->generateTable();
        $tableData[] = [];
        $tableData[] = [];

        $allTables = array_merge($allTables, $tableData);
    } catch (Exception $e) {
        dump($e->getMessage());
    }
}

if ($export) {
    $filename = '../files/' . time() . '.csv';

    if (Exporter::export($filename, $allTables, new CsvFormat)) {
        Exporter::download($filename);
    }
} else {
    echo HtmlFormat::format($allTables, ['header' => true]);
}
