<?php

namespace App\DataGenerator;

use Exception;
use InvalidArgumentException;
use OutOfRangeException;

class Table
{
    const MAX_DATA_TABLE_ROWS = 1000;
    const MAX_CLASSIFICATION_ROWS = 100;

    /**
     * @var Row[]
     */
    private $rows = [];

    private $classification = [];

    /**
     * Table constructor.
     * @param Row[] $rows
     */
    public function __construct($rows = [])
    {
        $this->rows = $rows;
    }

    /**
     * @param array $dataTable
     * @return Table
     */
    public static function fromArray(array $dataTable): Table
    {
        $instance = new self();

        foreach ($dataTable as $i => $category) {
            foreach ($category as $index => $rowNumbers) {
                $rowObject = (new Row)
                    ->setIndex($index)
                    ->setClass($i)
                ;

                foreach ($rowNumbers as $number) {
                    $rowObject->addNumber(new Number($number));
                }

                $instance->addRow($rowObject);
            }
        }

        return $instance;
    }

    /**
     * @param string $dataTable
     * @param string $classification
     * @return Table
     * @throws Exception
     */
    public static function fromExternal(string $dataTable, string $classification): Table
    {
        $instance = new self;

        $validDataTable = self::parseTable($dataTable);
        $validClassification = self::parseClassifications($classification, count($validDataTable));
        $rowsPerClass = self::getRowsPerClassification($validClassification);
        $instance->setClassification($validClassification);

        $class = 0;
        $iterator = 1;
        foreach ($validDataTable as $row) {
            $rowObject = new Row();

            if ($iterator <= $rowsPerClass[$class]) {
                $rowObject
                    ->setIndex($iterator - 1)
                    ->setClass($class)
                ;

                // If we reach the size of the current class element move to the next one
                if ($iterator == $rowsPerClass[$class]) {
                    $iterator = 1;
                    $class++;
                } else {
                    $iterator++;
                }
            }

            foreach ($row as $number) {
                $rowObject->addNumber(new Number($number));
            }

            $instance->addRow($rowObject);
        }

        return $instance;
    }

    /**
     * @param string $classification
     * @param int $rowsNeeded
     * @return array
     * @throws Exception
     */
    private static function parseClassifications(string $classification, int $rowsNeeded = null)
    {
        $csvRows = array_filter(
            array_map(
                'str_getcsv',
                explode("\n", trim($classification))
            )
        );

        $rowsSum = 0;
        $rowsPerClass = [];
        foreach ($csvRows as $index => $csvRow) {
            $csvRow = array_map(
                'floatval',
                array_map(
                    'trim',
                    $csvRow
                )
            );

            if (!is_array($csvRow) || count($csvRow) !== 3) {
                throw new Exception('Invalid column count. Columns must be exactly 3');
            }

            if (!is_numeric($csvRow[0]) || !is_numeric($csvRow[1]) || !is_numeric($csvRow[2])) {
                throw new InvalidArgumentException('Every value must be numeric');
            }

            $rowsSum += (int) $csvRow[2];
            $rowsPerClass[] = (int) $csvRow[2];
            $csvRows[$index] = [$csvRow[0], $csvRow[1], (int) $csvRow[2]];
        }

        if (!is_null($rowsNeeded) && $rowsSum !== $rowsNeeded) {
            throw new Exception('Rows sum in classification doesn\'t match rows of the table provided');
        }

        if (count($csvRows) > self::MAX_CLASSIFICATION_ROWS || count($csvRows) < 1) {
            throw new OutOfRangeException('Number of lines for classification must be between 1 and 100');
        }

        return $csvRows;
    }

    /**
     * @param array $classification
     * @return array
     */
    private static function getRowsPerClassification(array $classification)
    {
        return array_map('intval', array_column($classification, 2));
    }

    /**
     * @param string $dataTable
     * @return array
     * @throws Exception
     * @throws InvalidArgumentException
     */
    private static function parseTable(string $dataTable)
    {
        $csvRows = array_map(
            'str_getcsv',
            array_map(
                'trim',
                explode("\n", trim($dataTable))
            )
        );

        if (!is_array($csvRows) || empty($csvRows)) {
            throw new Exception('Data table provided is not valid');
        }

        if (count($csvRows) > self::MAX_DATA_TABLE_ROWS || count($csvRows) < 1) {
            throw new OutOfRangeException('Lines for data table must be between 1 and ' . self::MAX_DATA_TABLE_ROWS);
        }

        foreach ($csvRows as $csvRow) {
            if (!is_array($csvRow)) {
                throw new Exception('Data table provided is not valid');
            }

            if (count($csvRow) !== 12) {
                throw new InvalidArgumentException('Row doesn\'t have 12 cells (' . implode(', ', $csvRow) . ')');
            }

            foreach ($csvRow as $cell) {
                if (!is_numeric($cell) && !empty($cell)) {
                    throw new Exception('Non-empty cells must have numeric values');
                }
            }
        }

        return $csvRows;
    }

    /**
     * @param int $class
     * @return Row
     */
    public function getRandomUncrossedRow(int $class)
    {
        $uncrossed = [];
        foreach ($this->rows as $index => $row) {
            if ($row->getClass() == $class && !$row->isCrossed()) {
                $uncrossed[] = $row;
            }
        }

        return $uncrossed ? $uncrossed[array_rand($uncrossed)] : null;
    }

    /**
     * @param Row[] $rows
     * @return Table
     */
    public function setRows(array $rows): Table
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @return Row[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @param Row $row
     */
    public function addRow(Row $row)
    {
        $this->rows[] = $row;
    }

    /**
     * @param $index
     * @param bool $cross
     */
    public function setRowCross($index, bool $cross)
    {
        $this->rows[$index]->setIsCrossed($cross);
    }

    /**
     * @param int|null $classId
     * @return Table
     */
    public function resetRowsStatus(int $classId = null): Table
    {
        foreach ($this->rows as $row) {
            if (!is_null($classId) && $row->getClass() != $classId) {
                continue;
            }

            $row->setIsCrossed(false);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * @param array $classification
     * @return Table
     */
    public function setClassification(array $classification): Table
    {
        $this->classification = $classification;

        return $this;
    }
}