<?php

use App\DataExporter\Exporter;
use App\DataExporter\Format\CsvFormat;
use App\DataExporter\Format\HtmlFormat;
use App\DataGenerator\{Table, Generator};

require_once '../vendor/autoload.php';
ini_set('precision', 16);
error_reporting(E_ALL ^ E_NOTICE);

$isWithExternalData = isset($_POST['data_table']) && isset($_POST['classification'])
    && !empty(trim($_POST['data_table'])) && !empty(trim($_POST['classification']));

try {
    $generator = new Generator(
        $isWithExternalData
            ? Table::fromExternal($_POST['data_table'], $_POST['classification'])
            : Table::fromArray(require('../common/data_constants.php'))
    );

    $numberOfTables = isset($_POST['tables']) && !empty($_POST['tables']) ? (int) $_POST['tables'] : 1;
    $numberOfRows = isset($_POST['rows']) && !empty($_POST['rows']) ? (int) $_POST['rows'] : 20;
    $export = isset($_POST['download']) && $_POST['download'] == 1;

    if ($numberOfTables > 1000 || $numberOfTables < 1) {
        throw new OutOfRangeException('Tables number should be between 1 and 2000');
    }

    if ($numberOfRows > 100 || $numberOfRows < 1) {
        throw new OutOfRangeException('Rows number should be between 1 and 100');
    }

    $allTables = $generator->generateTables($numberOfTables, $numberOfRows);

    if ($export) {
        $filename = '../files/' . time() . '.csv';

        if (Exporter::export($filename, $allTables, new CsvFormat)) {
            Exporter::download($filename);
        }
    } else {
        echo HtmlFormat::format($allTables, ['header' => true]);
    }
} catch (Exception $e) {
    echo htmlspecialchars($e->getMessage());
}